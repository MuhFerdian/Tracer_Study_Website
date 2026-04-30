<?php

namespace App\Http\Controllers;

use App\Models\alumniModel;
use App\Models\userModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ManajemenAlumniController extends Controller
{
    public function list()
    {
        $alumni = alumniModel::select('prodi', 'nim', 'nama_alumni', 'alumni_id', 'tanggal_lulus');

        return DataTables::of($alumni)
            ->addIndexColumn()
            ->editColumn('tanggal_lulus', function ($alumni) {
                return $alumni->tanggal_lulus ? $alumni->tanggal_lulus->format('d-m-Y') : '';
            })
            ->addColumn('aksi', function ($alumni) {
                $btn = '<button onclick="modalAction(\'' . url('/admin/alumni/' . $alumni->alumni_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/admin/alumni/' . $alumni->alumni_id . '/delete_ajax') . '\')"  class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) //memberitahu bahwa kolomaksi adalah html
            ->make(true);
    }
    public function import()
    {
        return view('layoutAdmin.manajemenAlumni.importAlumni'); // TANPA layout, hanya isi modal!
    }
    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_user' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('file_user');

            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            $data = $sheet->toArray(null, false, true, true);

            $insert_alumni = [];
            $failed_rows = [];
            $seen_nim = [];

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

                if (isset($normalized['nim']) && isset($normalized['nama'])) {
                    $headerRow = $rowNumber;
                    $headerMap = $normalized;
                    break;
                }
            }

            if ($headerRow === null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Header file tidak dikenali. Pastikan ada kolom NIM dan NAMA.'
                ]);
            }

            $alumniRoleId = DB::table('role')->where('role_kode', 'ALM')->value('role_id');
            if (!$alumniRoleId) {
                $alumniRoleId = DB::table('role')->where('role_nama', 'Alumni')->value('role_id');
            }

            if (!$alumniRoleId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Role Alumni tidak ditemukan di tabel role.'
                ]);
            }

            if (count($data) > 1) {
                try {
                    foreach ($data as $rowNumber => $row) {
                        if ($rowNumber <= $headerRow) continue;

                        $nimCol = $headerMap['nim'] ?? null;
                        $namaCol = $headerMap['nama'] ?? null;
                        $prodiCol = $headerMap['prodi'] ?? null;
                        $emailCol = $headerMap['email'] ?? null;
                        $tglLulusCol = $headerMap['tanggal lulus'] ?? ($headerMap['tahun lulus'] ?? null);

                        $programStudi = $prodiCol ? trim((string) ($row[$prodiCol] ?? '')) : null;
                        $nim = $nimCol ? trim((string) ($row[$nimCol] ?? '')) : '';
                        $nama = $namaCol ? trim((string) ($row[$namaCol] ?? '')) : '';
                        $tanggalLulusExcel = $tglLulusCol ? ($row[$tglLulusCol] ?? null) : null;
                        $email = $emailCol ? trim((string) ($row[$emailCol] ?? '')) : '';

                        // Validasi dasar
                        if (empty($nim) && empty($nama)) {
                            continue;
                        }

                        if (empty($nim) || empty($nama)) {
                            $failed_rows[] = "Baris $rowNumber: NIM/Nama kosong";
                            continue;
                        }

                        if (isset($seen_nim[$nim])) {
                            $failed_rows[] = "Baris $rowNumber: NIM $nim duplikat di file";
                            continue;
                        }
                        $seen_nim[$nim] = true;

                        // Cek duplikat alumni di DB
                        if (alumniModel::where('nim', $nim)->exists()) {
                            $failed_rows[] = "Baris $rowNumber: NIM $nim sudah ada";
                            continue;
                        }

                        // Konversi tanggal lulus: dukung serial excel, tanggal string, atau tahun saja (YYYY)
                        $tanggalLulus = null;
                        if (!empty($tanggalLulusExcel)) {
                            if (is_numeric($tanggalLulusExcel)) {
                                $tanggalLulus = date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($tanggalLulusExcel));
                            } elseif (preg_match('/^\d{4}$/', trim((string) $tanggalLulusExcel))) {
                                $tanggalLulus = trim((string) $tanggalLulusExcel) . '-12-31';
                            } else {
                                $tanggalLulus = date('Y-m-d', strtotime($tanggalLulusExcel));
                            }
                        }

                        $emailFinal = $email !== '' ? $email : strtolower($nim) . '@alumni.local';
                        $generatedUsername = strtolower(str_replace(' ', '_', $nama));

                        // Insert/update user alumni
                        $user = userModel::where('username', $generatedUsername)->first();
                        if (!$user) {
                            $user = userModel::create([
                                'role_id' => $alumniRoleId,
                                'username' => $generatedUsername,
                                'nim' => $nim,
                                'name' => $nama,
                                'email' => $emailFinal,
                                'password' => Hash::make($nim),
                                'status' => 'pending',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        } else {
                            $updateUser = [
                                'role_id' => $alumniRoleId,
                                'name' => $nama,
                                'nim' => $nim,
                                'updated_at' => now(),
                            ];
                            if (!empty($emailFinal)) {
                                $updateUser['email'] = $emailFinal;
                            }
                            $user->update($updateUser);
                        }

                        $insert_alumni[] = [
                            'user_id' => $user->id,
                            'prodi' => $programStudi,
                            'nim' => $nim,
                            'nama_alumni' => $nama,
                            'tanggal_lulus' => $tanggalLulus,
                            'email' => $emailFinal,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    if (count($insert_alumni) > 0) {
                        alumniModel::insert($insert_alumni);
                    }


                    return response()->json([
                        'status' => true,
                        'message' => 'Import selesai. ' . count($insert_alumni) . ' data berhasil ditambahkan.',
                        'skipped' => $failed_rows
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Gagal import: ' . $e->getMessage()
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data yang ditemukan di file'
                ]);
            }
        }

        return redirect('/admin/alumni');
    }

    public function create_ajax()
    {
        return view('layoutAdmin.manajemenAlumni.createAlumni');
    }
    public function store(Request $request)
    {
        // Validasi data
        $validator = Validator::make($request->all(), [
            'prodi'          => 'required|in:TIF Nganjuk,MNA Nganjuk',
            'nim'            => 'required|min:5|unique:alumni,nim',
            'nama_alumni'    => 'required|min:3',
            'tanggal_lulus'  => 'required|date',
            'email'          => 'required|email|unique:alumni,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => 'Validasi gagal!',
                'msgField'  => $validator->errors(),
            ]);
        }

        try {
            $alumniRoleId = DB::table('role')->where('role_kode', 'ALM')->value('role_id');
            if (!$alumniRoleId) {
                $alumniRoleId = DB::table('role')->where('role_nama', 'Alumni')->value('role_id');
            }

            if (!$alumniRoleId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Role Alumni tidak ditemukan di tabel role.'
                ]);
            }

            $emailFinal = !empty($request->email)
                ? $request->email
                : strtolower($request->nim) . '@alumni.local';
            $generatedUsername = strtolower(str_replace(' ', '_', $request->nama_alumni));

            $user = userModel::create([
                'role_id'  => $alumniRoleId,
                'username' => $generatedUsername,
                'nim' => $request->nim,
                'name' => $request->nama_alumni,
                'email' => $emailFinal,
                'password' => Hash::make($request->nim),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            alumniModel::create([
                'user_id'       => $user->id,
                'prodi'         => $request->prodi,
                'nim'           => $request->nim,
                'nama_alumni'   => $request->nama_alumni,
                'tanggal_lulus' => $request->tanggal_lulus,
                'email'         => $emailFinal
            ]);
            return response()->json([
                'status'  => true,
                'message' => 'Data alumni berhasil disimpan!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error'   => $e->getMessage(),
            ]);
        }
    }
    public function edit($id)
    {
        $alumni = alumniModel::find($id);
        return view('layoutAdmin.manajemenAlumni.edit_ajax', compact('alumni'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'prodi'          => 'required|in:TIF Nganjuk,MNA Nganjuk',
            'nim'            => 'required|min:5|unique:alumni,nim,' . $id . ',alumni_id',
            'nama_alumni'    => 'required|min:3',
            'tanggal_lulus'  => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => 'Validasi gagal!',
                'msgField'  => $validator->errors(),
            ]);
        }

        try {
            $alumni = alumniModel::findOrFail($id);
            $generatedUsername = strtolower(str_replace(' ', '_', $request->nama_alumni));
            if ($alumni->nim !== $request->nim || $alumni->nama_alumni !== $request->nama_alumni) {
                $user = userModel::find($alumni->user_id);
                if (!$user) {
                    return response()->json([
                        'status' => false,
                        'message' => 'User terkait tidak ditemukan.'
                    ]);
                }

                $user->update([
                    'username' => $generatedUsername,
                    'nim' => $request->nim,
                    'password' => Hash::make($request->nim),
                ]);
            }
            $alumni->update([
                'prodi'         => $request->prodi,
                'nim'           => $request->nim,
                'nama_alumni'   => $request->nama_alumni,
                'tanggal_lulus' => $request->tanggal_lulus,
                'email'         => $request->email
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Data alumni berhasil diperbarui!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat memperbarui data.',
                'error'   => $e->getMessage(),
            ]);
        }
    }
    public function confirm_ajax(string $id)
    {
        $alumni = alumniModel::find($id);
        return view('layoutAdmin.manajemenAlumni.confirm', compact('alumni'));
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $alumni = alumniModel::find($id);
            $user = userModel::find($alumni->user_id);
            if (!$alumni && !$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            } else {
                $alumni->delete();
                $user->delete();
                return response()->json([
                    'status'  => true,
                    'message' => 'Data berhasil dihapus'
                ]);
            }
        }
        return redirect('/');
    }
}
