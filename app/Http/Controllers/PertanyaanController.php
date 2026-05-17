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
            $data = Question::with('options', 'details')->orderBy('urutan')->orderBy('id')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('question_display', function ($row) {
                    return $row->pertanyaan;
                })
                ->addColumn('options_display', function ($row) {
                    if ($row->type === 'matrix') {
                        if ($row->details->isEmpty()) {
                            return '-';
                        }

                        return $row->details->pluck('item_label')->implode(', ');
                    }

                    if ($row->options->isEmpty()) {
                        return '-';
                    }

                    return $row->options->pluck('label')->implode(', ');
                })
                ->addColumn('aksi', function ($row) {
                    $btn  = '<div class="d-flex gap-1 justify-content-center">';
                    $btn .= '<button onclick="modalEdit(\'' . url('/admin/pertanyaan/' . $row->id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button>';
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

}
