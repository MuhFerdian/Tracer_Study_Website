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
                    if ($row->options->isEmpty()) {
                        return '-';
                    }
                    return $row->options->pluck('label')->implode(', ');
                })
                ->addColumn('aksi', function ($row) {
                    $btn = '<button onclick="modalEdit(\'' . url('/admin/pertanyaan/' . $row->id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                    $btn .= '<button onclick="modalDelete(\'' . url('/admin/pertanyaan/' . $row->id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
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
                'kode_soal' => 'nullable|string|max:50',
                'question_text' => 'required|string|min:5|max:255',
                'type' => 'required|in:text,single,multiple,scale,matrix',
                'urutan' => 'nullable|integer|min:1',
                'options' => 'nullable|string',
                'matrix_items' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            // Validasi urutan - cek duplicate
            $urutan = $request->filled('urutan') ? (int) $request->urutan : 0;
            if ($urutan > 0) {
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

            $question = Question::create([
                'kode_soal' => $request->kode_soal,
                'pertanyaan' => $request->question_text,
                'type' => $request->type,
                'urutan' => $request->filled('urutan') ? (int) $request->urutan : 0,
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
                        'field_code_a' => $item['field_code_a'] ?? null,
                        'field_code_b' => $item['field_code_b'] ?? null,
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
                'kode_soal' => 'nullable|string|max:50',
                'question_text' => 'required|string|min:5|max:255',
                'type' => 'required|in:text,single,multiple,scale,matrix',
                'urutan' => 'nullable|integer|min:1',
                'options' => 'nullable|string',
                'matrix_items' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            // Validasi urutan - cek duplicate (kecuali untuk record yang sedang di-edit)
            $urutan = $request->filled('urutan') ? (int) $request->urutan : 0;
            if ($urutan > 0) {
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
                'kode_soal' => $request->kode_soal,
                'pertanyaan' => $request->question_text,
                'type' => $request->type,
                'urutan' => $request->filled('urutan') ? (int) $request->urutan : 0,
            ]);

            // =======================
// 🔔 KIRIM NOTIF KE USER
// =======================
$fcm = app(FcmService::class);

// ambil user yang BELUM isi survey
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

            // Hapus options lama dan buat yang baru
            if ($request->filled('options') && in_array($request->type, ['single', 'multiple'])) {
                $question->options()->delete();
                $options = $this->parseOptions($request->options);
                foreach ($options as $idx => $label) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'label' => $label,
                        'value' => $label,
                        'urutan' => $idx + 1,
                    ]);
                }
            } else if (in_array($request->type, ['text', 'scale'])) {
                // Hapus options jika type diubah ke text atau scale
                $question->options()->delete();
            }

            // Hapus matrix items lama dan buat yang baru
            if ($request->filled('matrix_items') && $request->type === 'matrix') {
                $question->details()->delete();
                $matrixItems = $this->parseMatrixItems($request->matrix_items);
                foreach ($matrixItems as $idx => $item) {
                    QuestionDetail::create([
                        'question_id' => $question->id,
                        'item_label' => $item['label'] ?? '',
                        'field_code_a' => $item['field_code_a'] ?? null,
                        'field_code_b' => $item['field_code_b'] ?? null,
                        'urutan' => $idx + 1,
                    ]);
                }
            } else if ($request->type !== 'matrix') {
                // Hapus matrix items jika type diubah dari matrix
                $question->details()->delete();
            }

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

        return redirect('/');
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