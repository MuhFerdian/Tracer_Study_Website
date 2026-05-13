<?php

namespace App\Http\Controllers;

use App\Models\alumniModel;
use App\Models\User;
use App\Models\Question;
use App\Models\Answer;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ManajemenAlumniController extends Controller
{
    /**
     * =========================================
     * LIST DATATABLE
     * =========================================
     */
    public function list()
    {
        $alumni = alumniModel::query()
            ->select([
                'id',
                'user_id',
                'nim',
                'nama',
                'prodi',
                'no_hp',
                'email',
                'angkatan',
                'tahun_lulus',
            ])
            ->withCount('answers');

        return DataTables::of($alumni)
            ->addIndexColumn()

            ->editColumn('tahun_lulus', function ($alumni) {
                return $alumni->tahun_lulus ?: '-';
            })

            ->addColumn('status_survey', function ($alumni) {
                $totalQuestions = \App\Models\Question::count();
                $answered = $alumni->answers_count;

                if ($answered === 0) {
                    return '<span class="badge bg-danger">Belum Mengisi</span>';
                } elseif ($answered < $totalQuestions) {
                    return '<span class="badge bg-warning text-dark">Sebagian (' . $answered . '/' . $totalQuestions . ')</span>';
                } else {
                    return '<span class="badge bg-success">Sudah Mengisi</span>';
                }
            })

            ->addColumn('aksi', function ($alumni) {

                $btn  = '<div class="d-flex gap-1 justify-content-center">';

                $btn .= '<button 
                        onclick="modalAction(\'' . url('/admin/alumni/' . $alumni->id . '/edit_ajax') . '\')" 
                        class="btn btn-warning btn-sm">
                        Edit
                    </button>';

                $btn .= '<button 
                        onclick="modalAction(\'' . url('/admin/alumni/' . $alumni->id . '/delete_ajax') . '\')" 
                        class="btn btn-danger btn-sm">
                        Hapus
                    </button>';

                $btn .= '</div>';

                return $btn;
            })

            ->rawColumns(['status_survey', 'aksi'])
            ->make(true);
    }

    /**
     * =========================================
     * VIEW IMPORT
     * =========================================
     */
    public function import()
    {
        return view('layoutAdmin.manajemenAlumni.importAlumni');
    }

    /**
     * =========================================
     * IMPORT EXCEL
     * =========================================
     */
    public function import_ajax(Request $request)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [

            'file_user' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:5120'
            ]
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'msgField' => $validator->errors()
            ]);
        }

        DB::beginTransaction();

        try {

            $file = $request->file('file_user');

            /**
             * =========================================
             * LOAD FILE EXCEL
             * =========================================
             */
            $reader = IOFactory::createReaderForFile(
                $file->getRealPath()
            );

            $reader->setReadDataOnly(true);

            $spreadsheet = $reader->load(
                $file->getRealPath()
            );

            $sheet = $spreadsheet->getActiveSheet();

            $data = $sheet->toArray(null, false, true, true);

            if (count($data) <= 1) {

                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data ditemukan'
                ]);
            }

            /**
             * =========================================
             * DETEKSI HEADER
             * =========================================
             */
            $headerRow = null;
            $headerMap = [];

            foreach ($data as $rowNumber => $row) {

                $normalized = [];

                foreach ($row as $col => $value) {

                    $key = strtolower(trim((string) $value));

                    if ($key !== '') {
                        $normalized[$key] = $col;
                    }
                }

                if (
                    isset($normalized['nim']) &&
                    (
                        isset($normalized['nama']) ||
                        isset($normalized['nama alumni'])
                    )
                ) {

                    $headerRow = $rowNumber;
                    $headerMap = $normalized;
                    break;
                }
            }

            if ($headerRow === null) {

                return response()->json([
                    'status' => false,
                    'message' =>
                        'Header tidak dikenali. Pastikan ada kolom NIM dan NAMA.'
                ]);
            }

            /**
             * =========================================
             * VARIABLE
             * =========================================
             */
            $failed_rows = [];
            $success = 0;
            $seen_nim = [];

            /**
             * =========================================
             * LOOP DATA
             * =========================================
             */
            foreach ($data as $rowNumber => $row) {

                if ($rowNumber <= $headerRow) {
                    continue;
                }

                /**
                 * =========================================
                 * MAPPING COLUMN
                 * =========================================
                 */
                $nimCol = $headerMap['nim'] ?? null;

                $namaCol =
                    $headerMap['nama']
                    ?? ($headerMap['nama alumni'] ?? null);

                $prodiCol = $headerMap['prodi'] ?? null;

                $emailCol = $headerMap['email'] ?? null;

                $alamatCol = $headerMap['alamat'] ?? null;

                $angkatanCol = $headerMap['angkatan'] ?? null;

                $noHpCol =
                    $headerMap['no hp']
                    ?? ($headerMap['no_hp'] ?? null);

                $tahunLulusCol =
                    $headerMap['tahun lulus']
                    ?? ($headerMap['tanggal lulus'] ?? null);

                /**
                 * =========================================
                 * AMBIL DATA
                 * =========================================
                 */
                $nim = $nimCol
                    ? trim((string) ($row[$nimCol] ?? ''))
                    : '';

                $nama = $namaCol
                    ? trim((string) ($row[$namaCol] ?? ''))
                    : '';

                $prodi = $prodiCol
                    ? trim((string) ($row[$prodiCol] ?? ''))
                    : null;

                $email = $emailCol
                    ? trim((string) ($row[$emailCol] ?? ''))
                    : null;

                $alamat = $alamatCol
                    ? trim((string) ($row[$alamatCol] ?? ''))
                    : null;

                $angkatan = $angkatanCol
                    ? trim((string) ($row[$angkatanCol] ?? ''))
                    : null;

                $no_hp = $noHpCol
                    ? trim((string) ($row[$noHpCol] ?? ''))
                    : null;

                $rawTahunLulus = $tahunLulusCol
                    ? ($row[$tahunLulusCol] ?? null)
                    : null;

                /**
                 * =========================================
                 * SKIP BARIS KOSONG
                 * =========================================
                 */
                if (empty($nim) && empty($nama)) {
                    continue;
                }

                /**
                 * =========================================
                 * VALIDASI DASAR
                 * =========================================
                 */
                if (empty($nim) || empty($nama)) {

                    $failed_rows[] =
                        "Baris $rowNumber: NIM atau Nama kosong";

                    continue;
                }

                /**
                 * =========================================
                 * DUPLIKAT DI FILE
                 * =========================================
                 */
                if (isset($seen_nim[$nim])) {

                    $failed_rows[] =
                        "Baris $rowNumber: NIM $nim duplikat di file";

                    continue;
                }

                $seen_nim[$nim] = true;

                /**
                 * =========================================
                 * FORMAT TAHUN LULUS
                 * =========================================
                 */
                $tahunLulus = null;

                if (!empty($rawTahunLulus)) {

                    $raw = trim((string) $rawTahunLulus);

                    // format 2025
                    if (
                        preg_match('/^\d{4}$/', $raw) &&
                        (int)$raw >= 1900 &&
                        (int)$raw <= 2100
                    ) {

                        $tahunLulus = (int) $raw;
                    }

                    // excel serial number
                    elseif (is_numeric($raw)) {

                        $timestamp =
                            Date::excelToTimestamp((float)$raw);

                        $tahunLulus =
                            (int) date('Y', $timestamp);
                    }

                    // string tanggal
                    else {

                        $timestamp = strtotime($raw);

                        if ($timestamp) {

                            $tahunLulus =
                                (int) date('Y', $timestamp);
                        }
                    }
                }

                /**
                 * =========================================
                 * VALIDASI PER ROW
                 * =========================================
                 */
                $rowValidator = Validator::make([

                    'nim' => $nim,
                    'nama' => $nama,
                    'prodi' => $prodi,
                    'email' => $email,
                    'angkatan' => $angkatan,
                    'tahun_lulus' => $tahunLulus,
                    'no_hp' => $no_hp,
                    'alamat' => $alamat,

                ], [

                    'nim' =>
                        'required|min:5|max:20|regex:/^[A-Za-z0-9]+$/',

                    'nama' =>
                        'required|min:3|max:100|regex:/^[\pL\s\.\-]+$/u',

                    'prodi' =>
                        'nullable|string|max:100',

                    'email' =>
                        'nullable|email|max:100',

                    'angkatan' =>
                        'nullable|integer|min:1900|max:2100',

                    'tahun_lulus' =>
                        'nullable|integer|min:1900|max:2100',

                    'no_hp' =>
                        'nullable|min:10|max:15|regex:/^[0-9+\-]+$/',

                    'alamat' =>
                        'nullable|string|max:255',
                ]);

                if ($rowValidator->fails()) {

                    $failed_rows[] =
                        "Baris $rowNumber: " .
                        implode(', ', $rowValidator->errors()->all());

                    continue;
                }

                /**
                 * =========================================
                 * INSERT / UPDATE
                 * =========================================
                 */
                alumniModel::updateOrCreate(

                    ['nim' => $nim],

                    [

                        'user_id' => null,

                        'prodi' => $prodi,

                        'nama' => $nama,

                        'angkatan' =>
                            is_numeric($angkatan)
                            ? (int) $angkatan
                            : null,

                        'tahun_lulus' => $tahunLulus,

                        'email' =>
                            !empty($email)
                            ? $email
                            : null,

                        'no_hp' =>
                            !empty($no_hp)
                            ? $no_hp
                            : null,

                        'alamat' =>
                            !empty($alamat)
                            ? $alamat
                            : null,
                    ]
                );

                $success++;
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "$success data berhasil diimport",
                'skipped' => $failed_rows
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal import data',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * =========================================
     * FORM CREATE
     * =========================================
     */
    public function create_ajax()
    {
        return view('layoutAdmin.manajemenAlumni.createAlumni');
    }

    /**
     * =========================================
     * STORE
     * =========================================
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'prodi' =>
                'required|string|max:100|regex:/^[a-zA-Z\s\-\.]+$/',

            'nim' =>
                'required|min:5|unique:alumni,nim|regex:/^[A-Za-z0-9]+$/',

            'nama_alumni' =>
                'required|min:3|max:100|regex:/^[a-zA-Z\s\-\.]+$/',

            'angkatan' =>
                'required|integer|min:1900|max:2100',

            'tanggal_lulus' =>
                'required|integer|min:1900|max:2100',

            'email' =>
                'nullable|email|max:100|unique:alumni,email',

            'no_hp' => 
                'nullable|min:10|max:15|regex:/^[0-9+\-]+$/',

            'alamat' => 
                'nullable|string|max:255',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'msgField' => $validator->errors(),
            ]);
        }

        try {

            alumniModel::create([

                'user_id' => null,

                'prodi' => $request->prodi,

                'nim' => $request->nim,

                'nama' => $request->nama_alumni,

                'angkatan' => $request->angkatan,

                'tahun_lulus' => $request->tanggal_lulus,

                'email' => $request->email ?: null,

                'no_hp' => $request->no_hp ?: null,

                'alamat' => $request->alamat ?: null,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data alumni berhasil disimpan'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan data',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * =========================================
     * FORM EDIT
     * =========================================
     */
    public function edit($id)
    {
        $alumni = alumniModel::findOrFail($id);

        return view(
            'layoutAdmin.manajemenAlumni.edit_ajax',
            compact('alumni')
        );
    }

    /**
     * =========================================
     * UPDATE
     * =========================================
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [

            'prodi' =>
                'required|string|max:100|regex:/^[a-zA-Z\s\-\.]+$/',

            'nim' =>
                'required|min:5|regex:/^[A-Za-z0-9]+$/|unique:alumni,nim,' . $id . ',id',

            'nama_alumni' =>
                'required|min:3|max:100|regex:/^[a-zA-Z\s\-\.]+$/',

            'angkatan' =>
                'required|integer|min:1900|max:2100',

            'tanggal_lulus' =>
                'required|integer|min:1900|max:2100',

            'email' =>
                'nullable|email|max:100|unique:alumni,email,' . $id . ',id',

            'no_hp' =>
                'nullable|min:10|max:15|regex:/^[0-9+\-]+$/',

            'alamat' =>
                'nullable|string|max:255',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'msgField' => $validator->errors(),
            ]);
        }

        try {

            $alumni = alumniModel::findOrFail($id);

            /**
             * UPDATE USER JIKA SUDAH REGISTER
             */
            if ($alumni->user_id) {

                $user = User::find($alumni->user_id);

                if ($user) {

                    $user->update([
                        'password' => Hash::make($request->nim),
                    ]);
                }
            }

            $alumni->update([

                'prodi' => $request->prodi,

                'nim' => $request->nim,

                'nama' => $request->nama_alumni,

                'angkatan' => $request->angkatan,

                'tahun_lulus' => $request->tanggal_lulus,

                'email' => $request->email ?: null,

                'no_hp' => $request->no_hp ?: null,

                'alamat' => $request->alamat ?: null,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diperbarui'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Gagal update data',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * =========================================
     * CONFIRM DELETE
     * =========================================
     */
    public function confirm_ajax(string $id)
    {
        if (!request()->ajax() && !request()->wantsJson()) {
            abort(403);
        }

        $alumni = alumniModel::find($id);

        if (!$alumni) {
            return response()->json([
                'status' => false,
                'message' => 'Data alumni tidak ditemukan'
            ]);
        }

        return view(
            'layoutAdmin.manajemenAlumni.confirm',
            compact('alumni')
        );
    }

    /**
     * =========================================
     * DELETE ALUMNI
     * =========================================
     */
    public function delete_ajax(Request $request, $id)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            abort(403);
        }

        try {

            DB::beginTransaction();

            $alumni = alumniModel::find($id);

            if (!$alumni) {

                return response()->json([
                    'status' => false,
                    'message' => 'Data alumni tidak ditemukan'
                ]);
            }

            /**
             * =========================================
             * DELETE RELASI JAWABAN
             * =========================================
             */
            Answer::where('alumni_id', $alumni->id)->delete();

            /**
             * =========================================
             * DELETE USER JIKA ADA
             * =========================================
             */
            if ($alumni->user_id) {

                $user = User::find($alumni->user_id);

                if ($user) {
                    $user->delete();
                }
            }

            /**
             * =========================================
             * DELETE ALUMNI
             * =========================================
             */
            $alumni->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data alumni berhasil dihapus'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * =========================================
     * DETAIL JAWABAN ALUMNI
     * =========================================
     */
    public function showAnswers($id)
    {
        $alumni = alumniModel::findOrFail($id);

        $questions = Question::select(
                'id',
                'pertanyaan',
                'type',
                'urutan'
            )
            ->with(['options' => function($q) { $q->orderBy('urutan'); }, 'details' => function($q) { $q->orderBy('urutan'); }])
            ->orderBy('urutan')
            ->get();

        $answers = Answer::where('alumni_id', $id)
            ->with(['answerDetails' => function($q) { $q->with('option'); }])
            ->get()
            ->keyBy('question_id');

        return view(
            'layoutAdmin.manajemenAlumni.detail_answers',
            compact('alumni', 'questions', 'answers')
        );
    }
}