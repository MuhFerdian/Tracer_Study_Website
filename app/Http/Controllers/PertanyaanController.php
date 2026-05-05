<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PertanyaanController extends Controller
{
    public function index()
    {
        return view('layoutAdmin.pertanyaan.index');
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $data = Question::with('options')->orderBy('urutan')->orderBy('id')->get();

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
                'question_text' => 'required|string',
                'type' => 'required|in:text,single,multiple,scale',
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

            return response()->json([
                'status' => true,
                'message' => 'Pertanyaan berhasil ditambahkan.'
            ]);
        }

        return redirect('/admin');
    }

    public function edit_ajax(string $id)
    {
        $data = Question::with('options')->findOrFail($id);
        return view('layoutAdmin.pertanyaan.edit', compact('data'));
    }

    public function update_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $validator = Validator::make($request->all(), [
                'kode_soal' => 'nullable|string|max:50',
                'question_text' => 'required|string',
                'type' => 'required|in:text,single,multiple,scale',
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

            $question = Question::findOrFail($id);
            $question->update([
                'kode_soal' => $request->kode_soal,
                'pertanyaan' => $request->question_text,
                'type' => $request->type,
                'urutan' => $request->filled('urutan') ? (int) $request->urutan : 0,
            ]);

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
}  