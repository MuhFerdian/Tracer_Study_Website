<?php

namespace App\Http\Controllers;

use App\Models\PertanyaanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PertanyaanController extends Controller
{
    public function index()
    {

        return view('layoutAdmin.pertanyaan.index'); // Buat file Blade ini
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $data = PertanyaanModel::orderBy('urutan')->orderBy('pertanyaan_id')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('question_display', function ($row) {
                    return $row->question_text;
                })
                ->addColumn('options_display', function ($row) {
                    if (empty($row->options)) {
                        return '-';
                    }

                    if (is_array($row->options)) {
                        return implode(', ', $row->options);
                    }

                    return $row->options;
                })
                ->addColumn('aksi', function ($row) {
                    $btn = '<button onclick="modalEdit(\'' . url('/admin/pertanyaan/' . $row->pertanyaan_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                    $btn .= '<button onclick="modalDelete(\'' . url('/admin/pertanyaan/' . $row->pertanyaan_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
                    return $btn;
                })

                ->rawColumns(['aksi']) // Kolom aksi berisi HTML
                ->make(true);
        }

        // Jika bukan AJAX, jangan return view atau HTML di sini
        return response()->json(['message' => 'Bukan permintaan AJAX'], 400);
    }
    public function create_ajax()
    {
        return view('layoutAdmin.pertanyaan.create'); // Buat file Blade ini
    }

    public function store(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $validator = Validator::make($request->all(), [
                'kode_soal' => 'required|string|max:50',
                'question_text' => 'required|string',
                'type' => 'required|in:text,radio,checkbox,number',
                'urutan' => 'nullable|integer|min:0',
                'options' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            PertanyaanModel::create([
                'kode_soal' => $request->kode_soal,
                'question_text' => $request->question_text,
                'type' => $request->type,
                'options' => $this->handleOptions($request),
                'urutan' => $request->filled('urutan') ? (int) $request->urutan : 0,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Pertanyaan berhasil ditambahkan.'
            ]);
        }

        return redirect('/admin');
    }

    public function edit_ajax(string $id)
    {
        $data = PertanyaanModel::findOrFail($id);
        return view('layoutAdmin.pertanyaan.edit', compact('data')); // Buat file Blade ini
    }

    public function update_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $validator = Validator::make($request->all(), [
                'kode_soal' => 'required|string|max:50',
                'question_text' => 'required|string',
                'type' => 'required|in:text,radio,checkbox,number',
                'urutan' => 'nullable|integer|min:0',
                'options' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $data = PertanyaanModel::findOrFail($id);
            $data->update([
                'kode_soal' => $request->kode_soal,
                'question_text' => $request->question_text,
                'type' => $request->type,
                'options' => $this->handleOptions($request),
                'urutan' => $request->filled('urutan') ? (int) $request->urutan : 0,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Pertanyaan berhasil diperbarui.'
            ]);
        }

        return redirect('/admin');
    }
    public function confirm_ajax(string $id)
    {
        $pertanyaan = PertanyaanModel::find($id);
        return view('layoutAdmin.pertanyaan.confirm', compact('pertanyaan')); // Buat file Blade ini
    }

    public function delete_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $data = PertanyaanModel::find($id);
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

    // Tambahkan di PertanyaanController
    public function getPertanyaan()
    {
        // Ambil semua data dari model Pertanyaan
        $data = PertanyaanModel::all();

        // Kembalikan data dalam format JSON
        return response()->json($data);
    }

    private function handleOptions(Request $request): ?array
    {
        if (!in_array($request->type, ['radio', 'checkbox'])) {
            return null;
        }

        if (!$request->filled('options')) {
            return [];
        }

        $decoded = json_decode($request->options, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter(array_map('trim', $decoded), function ($item) {
                return $item !== '';
            }));
        }

        return array_values(array_filter(array_map('trim', explode(',', $request->options)), function ($item) {
            return $item !== '';
        }));
    }
}