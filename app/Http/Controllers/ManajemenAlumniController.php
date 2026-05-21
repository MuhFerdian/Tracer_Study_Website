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
     * HITUNG TOTAL PERTANYAAN RELEVAN PER ALUMNI
     * (sama dengan logika filter di showAnswers)
     * =========================================
     */
    private function countRelevantQuestions(alumniModel $alumni): int
    {
        $allQuestions = Question::select('id', 'kode_soal', 'pertanyaan', 'type', 'urutan')
            ->where('is_archived', false)
            ->orderBy('urutan')
            ->get();

        // Ambil jawaban alumni untuk keperluan filter kondisional
        $answers = Answer::where('alumni_id', $alumni->id)
            ->with('answerDetails')
            ->get()
            ->keyBy('question_id');

        $getAnswerByKode = function (string $kode) use ($allQuestions, $answers): string {
            $q = $allQuestions->firstWhere('kode_soal', $kode);
            if (!$q) return '';
            $answer = $answers->get($q->id);
            if (!$answer) return '';
            $detail = $answer->answerDetails->first();
            return $detail ? ($detail->value ?? '') : '';
        };

        $status = $getAnswerByKode('f8');

        return $allQuestions->filter(function ($q) use ($status, $getAnswerByKode) {
            $kode = $q->kode_soal ?? '';

            if (in_array($kode, ['f502', 'f5a1', 'f5a2', 'f1101', 'f5b', 'f5d', 'f6', 'f7', 'f7a'])) {
                return str_contains($status, 'Bekerja');
            }
            if ($kode === 'f505') {
                return str_contains($status, 'Bekerja') || str_contains($status, 'Wiraswasta');
            }
            if (in_array($kode, ['f503', 'f5c'])) {
                return str_contains($status, 'Wiraswasta');
            }
            if (in_array($kode, ['f18a', 'f18b', 'f18c', 'f18d'])) {
                return str_contains($status, 'Melanjutkan Pendidikan');
            }
            if ($kode === 'f1102') return str_contains($getAnswerByKode('f1101'), 'Lainnya');
            if ($kode === 'f1202') return str_contains($getAnswerByKode('f1201'), 'Lainnya');
            if ($kode === 'f302')  return str_contains($getAnswerByKode('f301'), 'sebelum lulus');
            if ($kode === 'f303')  return str_contains($getAnswerByKode('f301'), 'sesudah lulus');
            if ($kode === 'f416')  return str_contains($getAnswerByKode('f401-f416'), 'Lainnya');
            if ($kode === 'f1002') return str_contains($getAnswerByKode('f1001'), 'Lainnya');
            if ($kode === 'f1614') return str_contains($getAnswerByKode('f1601-f1614'), 'Lainnya');

            return true;
        })->count();
    }

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
                'alamat',
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
                $answered = $alumni->answers_count;

                if ($answered === 0) {
                    return '<span class="badge bg-danger">Belum Mengisi</span>';
                }

                // Hitung total pertanyaan relevan untuk alumni ini
                $totalRelevant = $this->countRelevantQuestions($alumni);

                if ($answered >= $totalRelevant) {
                    return '<span class="badge bg-success">Sudah Mengisi</span>';
                } else {
                    return '<span class="badge bg-warning text-dark">Sebagian (' . $answered . '/' . $totalRelevant . ')</span>';
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

                    'nim'         => $nim,
                    'nama'        => $nama,
                    'prodi'       => $prodi,
                    'email'       => $email,
                    'angkatan'    => $angkatan,
                    'tahun_lulus' => $tahunLulus,
                    'no_hp'       => $no_hp,
                    'alamat'      => $alamat,

                ], [

                    'nim' =>
                        'required|string|min:5|max:20|regex:/^[A-Za-z0-9]+$/',

                    'nama' =>
                        'required|string|min:3|max:100|regex:/^[\pL\s\.\-\']+$/u',

                    'prodi' =>
                        'nullable|string|max:100|regex:/^[\pL\s\.\-]+$/u',

                    'email' =>
                        'nullable|email|max:100',

                    'angkatan' =>
                        'nullable|integer|min:1900|max:2100',

                    'tahun_lulus' =>
                        'nullable|integer|min:1900|max:2100',

                    'no_hp' =>
                        'nullable|string|min:10|max:13|regex:/^[0-9+\-]+$/',

                    'alamat' =>
                        'nullable|string|max:255',

                ], [
                    'nim.regex'   => 'NIM hanya boleh huruf dan angka.',
                    'nama.regex'  => 'Nama mengandung karakter tidak valid.',
                    'no_hp.regex' => 'No HP hanya boleh angka, +, dan -.',
                ]);

                if ($rowValidator->fails()) {

                    $failed_rows[] =
                        "Baris $rowNumber: " .
                        implode(', ', $rowValidator->errors()->all());

                    continue;
                }

                // Validasi logika: angkatan tidak boleh lebih besar dari tahun lulus
                if (
                    !empty($angkatan) && !empty($tahunLulus) &&
                    (int) $angkatan > (int) $tahunLulus
                ) {
                    $failed_rows[] = "Baris $rowNumber: Angkatan ($angkatan) tidak boleh lebih besar dari tahun lulus ($tahunLulus).";
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

            'nim' =>
                'required|string|min:5|max:20|unique:alumni,nim|regex:/^[A-Za-z0-9]+$/',

            'prodi' =>
                'required|string|max:100|regex:/^[\pL\s\.\-]+$/u',

            'nama_alumni' =>
                'required|string|min:3|max:100|regex:/^[\pL\s\.\-\']+$/u',

            'angkatan' =>
                'required|integer|min:1900|max:2100',

            'tanggal_lulus' =>
                'required|integer|min:1900|max:2100|gte:angkatan',

            'email' =>
                'nullable|email:rfc,dns|max:100|unique:alumni,email|regex:/^[a-zA-Z0-9._%+\-]+@gmail\.com$/i',

            'no_hp' =>
                'nullable|string|min:10|max:13|regex:/^[0-9+\-]+$/',

            'alamat' =>
                'nullable|string|max:255',

        ], [
            'nim.regex'            => 'NIM hanya boleh huruf dan angka.',
            'nim.unique'           => 'NIM sudah terdaftar.',
            'nama_alumni.regex'    => 'Nama hanya boleh huruf, spasi, titik, dan tanda hubung.',
            'prodi.regex'          => 'Program studi hanya boleh huruf, spasi, titik, dan tanda hubung.',
            'no_hp.regex'          => 'No HP hanya boleh angka, +, dan -.',
            'tanggal_lulus.gte'    => 'Tahun lulus tidak boleh lebih kecil dari angkatan.',
            'email.unique'         => 'Email sudah terdaftar.',
            'email.regex'          => 'Email harus menggunakan domain @gmail.com.',
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
        // Pastikan $id integer untuk mencegah injection di rule unique
        $id = (int) $id;

        $validator = Validator::make($request->all(), [

            'prodi' =>
                'required|string|max:100|regex:/^[\pL\s\.\-]+$/u',

            'nim' =>
                'required|string|min:5|max:20|regex:/^[A-Za-z0-9]+$/|unique:alumni,nim,' . $id . ',id',

            'nama_alumni' =>
                'required|string|min:3|max:100|regex:/^[\pL\s\.\-\']+$/u',

            'angkatan' =>
                'required|integer|min:1900|max:2100',

            'tanggal_lulus' =>
                'required|integer|min:1900|max:2100|gte:angkatan',

            'email' =>
                'nullable|email:rfc,dns|max:100|unique:alumni,email,' . $id . ',id|regex:/^[a-zA-Z0-9._%+\-]+@gmail\.com$/i',

            'no_hp' =>
                'nullable|string|min:10|max:13|regex:/^[0-9+\-]+$/',

            'alamat' =>
                'nullable|string|max:255',

        ], [
            'nim.regex'            => 'NIM hanya boleh huruf dan angka.',
            'nim.unique'           => 'NIM sudah digunakan alumni lain.',
            'nama_alumni.regex'    => 'Nama hanya boleh huruf, spasi, titik, dan tanda hubung.',
            'prodi.regex'          => 'Program studi hanya boleh huruf, spasi, titik, dan tanda hubung.',
            'no_hp.regex'          => 'No HP hanya boleh angka, +, dan -.',
            'tanggal_lulus.gte'    => 'Tahun lulus tidak boleh lebih kecil dari angkatan.',
            'email.unique'         => 'Email sudah digunakan alumni lain.',
            'email.regex'          => 'Email harus menggunakan domain @gmail.com.',
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
             * Catatan: password user TIDAK direset saat admin edit data alumni.
             * Hanya data profil alumni yang diperbarui.
             */
            if ($alumni->user_id) {
                $user = User::find($alumni->user_id);
                if ($user) {
                    // Hanya update nama jika berubah
                    $user->update([
                        'name' => $request->nama_alumni,
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

        $allQuestions = Question::select('id', 'kode_soal', 'pertanyaan', 'type', 'urutan')
            ->where('is_archived', false)
            ->with([
                'options'  => fn($q) => $q->orderBy('urutan'),
                'details'  => fn($q) => $q->orderBy('urutan'),
            ])
            ->orderBy('urutan')
            ->get();

        // Eager load semua jawaban + detail + option sekaligus — hindari N+1
        $answers = Answer::where('alumni_id', $id)
            ->with(['answerDetails.option'])
            ->get()
            ->keyBy('question_id');

        // Helper: ambil nilai jawaban berdasarkan kode soal
        $getAnswerByKode = function (string $kode) use ($allQuestions, $answers): string {
            $q = $allQuestions->firstWhere('kode_soal', $kode);
            if (!$q) return '';
            $answer = $answers->get($q->id);
            if (!$answer) return '';
            $detail = $answer->answerDetails->first();
            return $detail ? ($detail->value ?? '') : '';
        };

        // Filter pertanyaan berdasarkan logika kondisional Kemendikbud
        $status = $getAnswerByKode('f8');

        $questions = $allQuestions->filter(function ($q) use ($status, $getAnswerByKode) {
            $kode = $q->kode_soal ?? '';

            // Hanya tampil jika Bekerja
            if (in_array($kode, ['f502', 'f5a1', 'f5a2', 'f1101', 'f5b', 'f5d', 'f6', 'f7', 'f7a'])) {
                return str_contains($status, 'Bekerja');
            }

            // f505 tampil untuk Bekerja DAN Wiraswasta
            if ($kode === 'f505') {
                return str_contains($status, 'Bekerja') || str_contains($status, 'Wiraswasta');
            }

            // Hanya tampil jika Wiraswasta
            if (in_array($kode, ['f503', 'f5c'])) {
                return str_contains($status, 'Wiraswasta');
            }

            // Hanya tampil jika Melanjutkan Pendidikan
            if (in_array($kode, ['f18a', 'f18b', 'f18c', 'f18d'])) {
                return str_contains($status, 'Melanjutkan Pendidikan');
            }

            // Kondisional lainnya
            if ($kode === 'f1102') return str_contains($getAnswerByKode('f1101'), 'Lainnya');
            if ($kode === 'f1202') return str_contains($getAnswerByKode('f1201'), 'Lainnya');
            if ($kode === 'f302')  return str_contains($getAnswerByKode('f301'), 'sebelum lulus');
            if ($kode === 'f303')  return str_contains($getAnswerByKode('f301'), 'sesudah lulus');
            if ($kode === 'f416')  return str_contains($getAnswerByKode('f401-f416'), 'Lainnya');
            if ($kode === 'f1002') return str_contains($getAnswerByKode('f1001'), 'Lainnya');
            if ($kode === 'f1614') return str_contains($getAnswerByKode('f1601-f1614'), 'Lainnya');

            return true;
        })->values();

        return view(
            'layoutAdmin.manajemenAlumni.detail_answers',
            compact('alumni', 'questions', 'answers')
        );
    }
}