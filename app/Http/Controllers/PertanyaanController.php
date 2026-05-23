<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Services\FcmService;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PertanyaanController extends Controller
{
    public function index()
    {
        return view('layoutAdmin.pertanyaan.index');
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            // Hanya tampilkan pertanyaan yang TIDAK diarsip
            $data = Question::with('options', 'details')
                ->where('is_archived', false)
                ->orderBy('urutan')->orderBy('id')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('question_display', function ($row) {
                    return $row->pertanyaan;
                })
                ->addColumn('options_display', function ($row) {
                    if ($row->type === 'matrix') {
                        if ($row->details->isEmpty()) return '-';
                        return $row->details->pluck('item_label')->implode(', ');
                    }
                    if ($row->options->isEmpty()) return '-';
                    return $row->options->pluck('label')->implode(', ');
                })
                ->addColumn('aksi', function ($row) {
                    $btn  = '<div class="d-flex gap-1 justify-content-center">';
                    $btn .= '<button onclick="modalEdit(\'' . url('/admin/pertanyaan/' . $row->id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button>';
                    $btn .= '<button onclick="arsipPertanyaan(' . $row->id . ')" class="btn btn-secondary btn-sm"><i class="fas fa-archive me-1"></i>Arsip</button>';
                    $btn .= '<button onclick="modalDelete(\'' . url('/admin/pertanyaan/' . $row->id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return response()->json(['message' => 'Bukan permintaan AJAX'], 400);
    }
    public function create_ajax()
    {
        return view('layoutAdmin.pertanyaan.create');
    }

    public function store(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $validator = Validator::make($request->all(), [
                'kode_soal'     => 'nullable|string|max:50|unique:questions,kode_soal',
                'question_text' => 'required|string|min:5|max:500|unique:questions,pertanyaan',
                'hint'          => 'nullable|string|max:500',
                'type'          => 'required|in:text,single,multiple,scale,matrix',
                'tipe_data'     => 'nullable|in:text,number,date,year',
                'urutan'        => 'nullable|integer|min:1',
                'options'       => 'nullable|string|max:2000',
                'matrix_items'  => 'nullable|string|max:2000',
            ], [
                'kode_soal.unique'     => 'Kode soal sudah digunakan.',
                'question_text.unique' => 'Pertanyaan yang sama sudah ada.',
                'question_text.min'    => 'Pertanyaan minimal 5 karakter.',
                'type.in'              => 'Tipe tidak valid.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            // Tentukan urutan - jika kosong, gunakan max + 1
            $urutan = $request->filled('urutan') ? (int) $request->urutan : null;
            if ($urutan === null || $urutan <= 0) {
                // Auto-increment ke urutan terakhir + 1
                $maxUrutan = Question::max('urutan') ?? 0;
                $urutan = $maxUrutan + 1;
            } else {
                // Validasi duplicate jika urutan diisi manual
                $urutanExists = Question::where('urutan', $urutan)->exists();
                if ($urutanExists) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validasi gagal',
                        'msgField' => ['urutan' => ['Urutan ' . $urutan . ' sudah digunakan. Silakan gunakan urutan yang berbeda.']]
                    ]);
                }
            }

            // Validasi matrix items
            if ($request->type === 'matrix') {
                if (!$request->filled('matrix_items')) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validasi gagal',
                        'msgField' => ['matrix_items' => ['Minimal harus ada 1 item untuk tipe matrix']]
                    ]);
                }

                $matrixItems = $this->parseMatrixItems($request->matrix_items);
                if (count($matrixItems) === 0) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validasi gagal',
                        'msgField' => ['matrix_items' => ['Format matrix items tidak valid']]
                    ]);
                }
            }

            // Validasi options berdasarkan type
            $type = $request->type;
            $options = $request->filled('options') ? $request->options : '';

            if (in_array($type, ['single', 'multiple'])) {
                if (empty($options)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validasi gagal',
                        'msgField' => ['options' => ['Options wajib diisi untuk tipe ' . $type]]
                    ]);
                }

                $parsedOptions = $this->parseOptions($options);
                if (count($parsedOptions) < 2) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validasi gagal',
                        'msgField' => ['options' => ['Minimal harus ada 2 opsi untuk tipe ' . $type]]
                    ]);
                }
            }

            // Tentukan tipe_data default berdasarkan type jika tidak diisi
            $tipeData = $request->filled('tipe_data') ? $request->tipe_data : 'text';

            $question = Question::create([
                'kode_soal'   => $request->kode_soal,
                'pertanyaan'  => $request->question_text,
                'hint'        => $request->hint,
                'type'        => $request->type,
                'tipe_data'   => $tipeData,
                'urutan'      => $urutan,
                'is_required' => true,
            ]);

            // ===============================
            // 🔔 NOTIFIKASI PERTANYAAN BARU
            // ===============================
            try {
                $fcm = app(FcmService::class);

                $users = User::whereNotNull('fcm_token')
                    ->whereHas('alumni') // hanya alumni (hapus jika semua user)
                    ->get();

                foreach ($users as $user) {
                    $fcm->sendToUser(
                        $user->id,
                        "Pertanyaan Baru 📢",
                        "Ada pertanyaan baru di survey tracer study, segera isi!"
                    );
                }
            } catch (\Throwable $e) {
                Log::warning('FCM gagal kirim notif pertanyaan baru: ' . $e->getMessage());
            }


            // Jika ada options, simpan ke question_options
            if ($request->filled('options') && in_array($request->type, ['single', 'multiple'])) {
                $options = $this->parseOptions($request->options);
                foreach ($options as $idx => $label) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'label' => $label,
                        'value' => $label,
                        'urutan' => $idx + 1,
                    ]);
                }
            }

            // Jika ada matrix items, simpan ke question_details
            if ($request->filled('matrix_items') && $request->type === 'matrix') {
                $matrixItems = $this->parseMatrixItems($request->matrix_items);
                foreach ($matrixItems as $idx => $item) {
                    QuestionDetail::create([
                        'question_id' => $question->id,
                        'item_label' => $item['label'] ?? '',
                        'urutan' => $idx + 1,
                    ]);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Pertanyaan berhasil ditambahkan.'
            ]);
        }

        return redirect('/admin');
    }

    public function edit_ajax(string $id)
    {
        $data = Question::with('options', 'details')->findOrFail($id);
        return view('layoutAdmin.pertanyaan.edit', compact('data'));
    }

    public function update_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $validator = Validator::make($request->all(), [
                'kode_soal'     => 'nullable|string|max:50|unique:questions,kode_soal,' . $id,
                'question_text' => 'required|string|min:5|max:500|unique:questions,pertanyaan,' . $id,
                'hint'          => 'nullable|string|max:500',
                'type'          => 'required|in:text,single,multiple,scale,matrix',
                'tipe_data'     => 'nullable|in:text,number,date,year',
                'urutan'        => 'nullable|integer|min:1',
                'options'       => 'nullable|string|max:2000',
                'matrix_items'  => 'nullable|string|max:2000',
            ], [
                'kode_soal.unique'     => 'Kode soal sudah digunakan pertanyaan lain.',
                'question_text.unique' => 'Pertanyaan yang sama sudah ada.',
                'question_text.min'    => 'Pertanyaan minimal 5 karakter.',
                'type.in'              => 'Tipe tidak valid.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            // Tentukan urutan - jika kosong, gunakan max + 1
            $urutan = $request->filled('urutan') ? (int) $request->urutan : null;
            if ($urutan === null || $urutan <= 0) {
                // Auto-increment ke urutan terakhir + 1
                $maxUrutan = Question::max('urutan') ?? 0;
                $urutan = $maxUrutan + 1;
            } else {
                // Validasi duplicate jika urutan diisi manual (kecuali untuk record yang sedang di-edit)
                $urutanExists = Question::where('urutan', $urutan)
                    ->where('id', '!=', $id)
                    ->exists();
                if ($urutanExists) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validasi gagal',
                        'msgField' => ['urutan' => ['Urutan ' . $urutan . ' sudah digunakan. Silakan gunakan urutan yang berbeda.']]
                    ]);
                }
            }

            // Validasi matrix items
            if ($request->type === 'matrix') {
                if (!$request->filled('matrix_items')) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validasi gagal',
                        'msgField' => ['matrix_items' => ['Minimal harus ada 1 item untuk tipe matrix']]
                    ]);
                }

                $matrixItems = $this->parseMatrixItems($request->matrix_items);
                if (count($matrixItems) === 0) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validasi gagal',
                        'msgField' => ['matrix_items' => ['Format matrix items tidak valid']]
                    ]);
                }
            }

            // Validasi options berdasarkan type
            $type = $request->type;
            $options = $request->filled('options') ? $request->options : '';

            if (in_array($type, ['single', 'multiple'])) {
                if (empty($options)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validasi gagal',
                        'msgField' => ['options' => ['Options wajib diisi untuk tipe ' . $type]]
                    ]);
                }

                $parsedOptions = $this->parseOptions($options);
                if (count($parsedOptions) < 2) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validasi gagal',
                        'msgField' => ['options' => ['Minimal harus ada 2 opsi untuk tipe ' . $type]]
                    ]);
                }
            }

            $question = Question::findOrFail($id);
            $question->update([
                'kode_soal'   => $request->kode_soal,
                'pertanyaan'  => $request->question_text,
                'hint'        => $request->hint,
                'type'        => $request->type,
                'tipe_data'   => $request->filled('tipe_data') ? $request->tipe_data : 'text',
                'urutan'      => $urutan,
            ]);

            // =======================
            // 🔔 KIRIM NOTIF KE USER (optional, skip jika FCM tidak dikonfigurasi)
            // =======================
            try {
                $credentialsPath = storage_path("app/firebase/service-account.json");
                if (file_exists($credentialsPath)) {
                    $fcm = app(FcmService::class);
                    $users = User::whereNotNull('fcm_token')
                        ->whereDoesntHave('alumni.answers')
                        ->get();
                    foreach ($users as $user) {
                        $fcm->sendToUser(
                            $user->id,
                            "Survey Belum Diisi ⚠️",
                            "Ada perubahan pertanyaan, segera isi survey ya!"
                        );
                    }
                }
            } catch (\Throwable $e) {
                // FCM gagal tidak boleh menghentikan proses update
                Log::warning('FCM notification gagal: ' . $e->getMessage());
            }

            // Hapus semua options dan details lama dulu, lalu isi ulang sesuai tipe baru
            $question->options()->delete();
            $question->details()->delete();

            if (in_array($request->type, ['single', 'multiple'])) {
                // Simpan options baru
                $options = $this->parseOptions($request->options);
                foreach ($options as $idx => $label) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'label'       => $label,
                        'value'       => $label,
                        'urutan'      => $idx + 1,
                    ]);
                }
            } elseif ($request->type === 'matrix' && $request->filled('matrix_items')) {
                // Simpan matrix items baru
                $matrixItems = $this->parseMatrixItems($request->matrix_items);
                foreach ($matrixItems as $idx => $item) {
                    QuestionDetail::create([
                        'question_id' => $question->id,
                        'item_label'  => $item['label'] ?? '',
                        'urutan'      => $idx + 1,
                    ]);
                }
            }
            // Untuk tipe text dan scale tidak perlu options/details

            return response()->json([
                'status' => true,
                'message' => 'Pertanyaan berhasil diperbarui.'
            ]);
        }

        return redirect('/admin');
    }
    public function confirm_ajax(string $id)
    {
        $pertanyaan = Question::findOrFail($id);
        return view('layoutAdmin.pertanyaan.confirm', compact('pertanyaan'));
    }

    public function delete_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $data = Question::find($id);
            if (!$data) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan.'
                ]);
            }

            $data->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus.'
            ]);
        }

        // return redirect('/admin');
        return response()->json([
            'status' => false,
            'message' => 'Invalid request'
        ], 400);
    }

    public function getPertanyaan()
    {
        $data = Question::with('options')->orderBy('urutan')->get();
        return response()->json($data);
    }

    public function checkUrutan(Request $request)
    {
        $urutan = $request->input('urutan');
        $excludeId = $request->input('excludeId');

        $query = Question::where('urutan', $urutan);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $exists = $query->exists();

        return response()->json([
            'available' => !$exists,
            'urutan' => $urutan
        ]);
    }

    private function parseOptions(string $optionsInput): array
    {
        $decoded = json_decode($optionsInput, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter(array_map('trim', $decoded), function ($item) {
                return $item !== '';
            }));
        }

        return array_values(array_filter(array_map('trim', explode(',', $optionsInput)), function ($item) {
            return $item !== '';
        }));
    }

    private function parseMatrixItems(string $matrixInput): array
    {
        $decoded = json_decode($matrixInput, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter($decoded, function ($item) {
                return !empty($item['label']);
            }));
        }

        return [];
    }

    // =============================================
    // ARSIP
    // =============================================

    public function arsip_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $data = Question::find($id);

            if (!$data) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan.'
                ]);
            }

            $data->update([
                'is_archived' => true
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Pertanyaan berhasil diarsip.'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid request'
        ], 400);
    }

    public function restore_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $data = Question::find($id);

            if (!$data) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan.'
                ]);
            }

            $data->update([
                'is_archived' => false
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Pertanyaan berhasil dipulihkan.'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid request'
        ], 400);
    }

    public function arsip_index()
    {
        return view('layoutAdmin.pertanyaan.arsip');
    }

    // public function arsip_export()
    // {
    //     $questions = Question::with('options', 'details')
    //         ->where('is_archived', true)
    //         ->orderBy('urutan')
    //         ->orderBy('id')
    //         ->get();

    //     $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $sheet->setTitle('Arsip Pertanyaan');

    //     // Header
    //     $headers = ['No', 'Kode Soal', 'Pertanyaan', 'Tipe', 'Opsi / Item Matrix', 'Urutan'];
    //     foreach ($headers as $col => $header) {
    //         $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . '1';
    //         $sheet->setCellValue($cell, $header);
    //     }

    //     $sheet->getStyle('A1:F1')->applyFromArray([
    //         'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    //         'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1557c0']],
    //         'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    //     ]);

    //     // Data rows
    //     foreach ($questions as $idx => $q) {
    //         $row = $idx + 2;

    //         if ($q->type === 'matrix') {
    //             $opsi = $q->details->pluck('item_label')->implode(', ');
    //         } else {
    //             $opsi = $q->options->pluck('label')->implode(', ');
    //         }

    //         $sheet->setCellValue('A' . $row, $idx + 1);
    //         $sheet->setCellValue('B' . $row, $q->kode_soal ?? '-');
    //         $sheet->setCellValue('C' . $row, $q->pertanyaan);
    //         $sheet->setCellValue('D' . $row, $q->type);
    //         $sheet->setCellValue('E' . $row, $opsi ?: '-');
    //         $sheet->setCellValue('F' . $row, $q->urutan);

    //         // Zebra stripe
    //         if ($idx % 2 === 0) {
    //             $sheet->getStyle("A{$row}:F{$row}")->getFill()
    //                 ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    //                 ->getStartColor()->setRGB('f0f6ff');
    //         }
    //     }

    //     // Auto size
    //     foreach (range(1, 6) as $col) {
    //         $sheet->getColumnDimension(
    //             \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col)
    //         )->setAutoSize(true);
    //     }

    //     // Border seluruh tabel
    //     $lastRow = $questions->count() + 1;
    //     $sheet->getStyle("A1:F{$lastRow}")->getBorders()->getAllBorders()
    //         ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

    //     $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    //     $filename = 'arsip_pertanyaan_' . date('d-m-Y') . '.xlsx';

    //     return response()->streamDownload(
    //         fn() => $writer->save('php://output'),
    //         $filename,
    //         ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
    //     );
    // }

    public function arsip_export()
    {
        $questions = Question::with('options', 'details')
            ->where('is_archived', true)
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        if ($questions->isEmpty()) {

            return redirect()
                ->back()
                ->with('error', 'Data arsip pertanyaan masih kosong.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Arsip Pertanyaan');

        // =========================================
        // TITLE
        // =========================================
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'ARSIP PERTANYAAN TRACER STUDY');

        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue(
            'A2',
            'Politeknik Negeri Jember - Teknologi Informasi'
        );

        $sheet->mergeCells('A3:F3');
        $sheet->setCellValue(
            'A3',
            'Tanggal Export : ' . date('d-m-Y H:i')
        );

        // STYLE TITLE
        $sheet->getStyle('A1:F3')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F172A']
            ]
        ]);

        $sheet->getStyle('A1')->getFont()->setSize(18);
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A3')->getFont()->setSize(10);

        // =========================================
        // HEADER TABLE
        // =========================================
        $headers = [
            'No',
            'Kode Soal',
            'Pertanyaan',
            'Tipe',
            'Opsi / Item Matrix',
            'Urutan'
        ];

        foreach ($headers as $col => $header) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . '5';
            $sheet->setCellValue($cell, $header);
        }

        // HEADER STYLE
        $sheet->getStyle('A5:F5')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1557C0']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB']
                ]
            ]
        ]);

        // =========================================
        // DATA
        // =========================================
        foreach ($questions as $idx => $q) {

            $row = $idx + 6;

            // Tentukan isi kolom Opsi / Item Matrix sesuai tipe pertanyaan
            if ($q->type === 'matrix') {
                $opsi = $q->details->pluck('item_label')->implode(', ');
                if (empty($opsi)) $opsi = '-';
            } elseif ($q->type === 'scale') {
                // Scale: ambil dari options jika ada, jika tidak tampilkan keterangan skala
                $optLabels = $q->options->pluck('label')->filter()->implode(', ');
                $opsi = $optLabels ?: 'Skala Penilaian (1-5)';
            } elseif ($q->type === 'text') {
                $opsi = 'Jawaban Teks Bebas';
            } else {
                // single / multiple
                $opsi = $q->options->pluck('label')->implode(', ');
                if (empty($opsi)) $opsi = '-';
            }

            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $q->kode_soal ?? '-');
            $sheet->setCellValue('C' . $row, $q->pertanyaan);
            $sheet->setCellValue('D' . $row, strtoupper($q->type));
            $sheet->setCellValue('E' . $row, $opsi ?: '-');
            $sheet->setCellValue('F' . $row, $q->urutan);

            // Zebra stripe
            if ($idx % 2 == 0) {
                $sheet->getStyle("A{$row}:F{$row}")
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('F8FAFC');
            }

            // Wrap text
            $sheet->getStyle("C{$row}:E{$row}")
                ->getAlignment()
                ->setWrapText(true);

            // Vertical center
            $sheet->getStyle("A{$row}:F{$row}")
                ->getAlignment()
                ->setVertical(
                    \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                );
        }

        // =========================================
        // BORDER
        // =========================================
        $lastRow = $questions->count() + 5;

        $sheet->getStyle("A5:F{$lastRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );

        // =========================================
        // AUTO SIZE
        // =========================================
        foreach (range(1, 6) as $col) {

            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);

            $sheet->getColumnDimension($column)
                ->setAutoSize(true);
        }

        // =========================================
        // ROW HEIGHT
        // =========================================
        foreach (range(5, $lastRow) as $row) {
            $sheet->getRowDimension($row)
                ->setRowHeight(-1);
        }

        // =========================================
        // FREEZE HEADER — FIX: Tambah 'A6' sebagai topLeftCell
        // Tanpa parameter kedua, Excel mulai scrollable pane dari A1
        // sehingga header tampak ganda (muncul di frozen area DAN scrollable area).
        // Cek juga active cell / selected cell agar Excel tidak memaksa scroll ke A1.
        // =========================================
        $sheet->freezePane('A6', 'A6');
        $sheet->setSelectedCell('A6');

        // =========================================
        // EXPORT
        // =========================================
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'arsip_pertanyaan_' . date('d-m-Y') . '.xlsx';

        return response()->streamDownload(
            fn() => $writer->save('php://output'),
            $filename,
            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]
        );
    }

    public function arsip_list(Request $request)
    {
        if ($request->ajax()) {

            $data = Question::with('options', 'details')
                ->where('is_archived', true)
                ->orderBy('urutan')
                ->orderBy('id')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('question_display', function ($row) {
                    return $row->pertanyaan;
                })

                ->addColumn('options_display', function ($row) {

                    if ($row->type === 'matrix') {
                        if ($row->details->isEmpty()) return '-';
                        return $row->details->pluck('item_label')->implode(', ');
                    }

                    if ($row->type === 'scale') {
                        $labels = $row->options->pluck('label')->filter()->implode(', ');
                        return $labels ?: 'Skala Penilaian (1-5)';
                    }

                    if ($row->type === 'text') {
                        return 'Jawaban Teks Bebas';
                    }

                    // single / multiple
                    if ($row->options->isEmpty()) return '-';
                    return $row->options->pluck('label')->implode(', ');
                })

                ->addColumn('aksi', function ($row) {
                    $btn  = '<div class="d-flex gap-1 justify-content-center">';
                    $btn .= '<button onclick="restorePertanyaan(' . $row->id . ')" class="btn btn-success btn-sm"><i class="fas fa-undo me-1"></i>Pulihkan</button>';
                    $btn .= '<button onclick="modalDelete(\'' . url('/admin/pertanyaan/' . $row->id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
                    $btn .= '</div>';
                    return $btn;
                })

                ->rawColumns(['aksi'])
                ->make(true);
        }

        return response()->json([
            'message' => 'Bukan permintaan AJAX'
        ], 400);
    }
}
