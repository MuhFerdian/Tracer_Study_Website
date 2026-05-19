<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ManajemenDosenController extends Controller
{
    // ======================
    // GET ROLE DOSEN
    // ======================
    private function getDosenRoleId()
    {
        return DB::table('role')
            ->where('role_nama', 'Dosen')
            ->value('role_id');
    }

    // ======================
    // INDEX
    // ======================
    public function index()
    {
        return view('layoutAdmin.manajemenDosen.index');
    }

    // ======================
    // DATATABLE
    // ======================
    public function list()
    {
        $roleId = $this->getDosenRoleId();

        if (!$roleId) {
            return response()->json(['data' => []]);
        }

        $dosen = User::where('role_id', $roleId)
            ->select('id', 'username', 'name', 'email', 'status');

        return DataTables::of($dosen)
            ->addIndexColumn()

            ->addColumn('status', function ($d) {
                return $d->status == 'active'
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-warning">Pending</span>';
            })

            // ->addColumn('aksi', function ($d) {
            //     return '
            //         <button onclick="modalAction(\'/admin/manajemen-dosen/'.$d->id.'/edit_ajax\')" class="btn btn-warning btn-sm">Edit</button>
            //         <button onclick="modalAction(\'/admin/manajemen-dosen/'.$d->id.'/delete_ajax\')" class="btn btn-danger btn-sm">Hapus</button>
            //     ';
            // })
            ->addColumn('aksi', function ($d) {
                return '
                    <button onclick="edit('.$d->id.')" class="btn btn-warning btn-sm">Edit</button>
                    <button onclick="hapus('.$d->id.')" class="btn btn-danger btn-sm">Hapus</button>
                ';
            })

            ->rawColumns(['status', 'aksi'])
            ->make(true);
    }

    // ======================
    // CREATE POPUP
    // ======================
    public function create_ajax()
    {
        return view('layoutAdmin.manajemenDosen.create');
    }

    // ======================
    // STORE
    // ======================
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:4|max:50|unique:users,username|regex:/^[A-Za-z0-9._]+$/',
            'name'     => 'required|string|min:3|max:100|regex:/^[\pL\s\.\-\']+$/u',
            'email'    => 'nullable|email:rfc,dns|max:100|unique:users,email',
            'password' => 'required|string|min:8|max:16',
            'status'   => 'required|in:pending,active',
        ], [
            'username.regex'   => 'Username hanya boleh huruf, angka, titik, dan underscore.',
            'username.unique'  => 'Username sudah digunakan.',
            'name.regex'       => 'Nama hanya boleh huruf, spasi, titik, dan tanda hubung.',
            'email.unique'     => 'Email sudah digunakan.',
            'password.min'     => 'Password minimal 8 karakter.',
            'password.max'     => 'Password maximal 16 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'message'  => 'Validasi gagal',
                'msgField' => $validator->errors(),
            ]);
        }

        $roleId = $this->getDosenRoleId();

        if (!$roleId) {
            return response()->json([
                'status'  => false,
                'message' => 'Role dosen tidak ditemukan'
            ]);
        }

        User::create([
            'role_id'  => $roleId,
            'username' => $request->username,
            'name'     => $request->name,
            'email'    => $request->email ?: null,
            'password' => Hash::make($request->password),
            'status'   => $request->status,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Dosen berhasil ditambahkan'
        ]);
    }

    // ======================
    // EDIT POPUP
    // ======================
    public function edit_ajax($id)
    {
        $user = User::findOrFail($id);
        return view('layoutAdmin.manajemenDosen.edit', compact('user'));
    }

    // ======================
    // UPDATE
    // ======================
    public function update(Request $request, $id)
    {
        $id   = (int) $id;
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:6|max:25|unique:users,username,' . $id . '|regex:/^[a-z0-9._]+$/',
            'name'     => 'required|string|min:3|max:100|regex:/^[\pL\s\.\-\']+$/u',
            'email'    => 'nullable|email:rfc,dns|max:100|unique:users,email,' . $id,
            'password' => 'required|string|min:8|max:16',
            'status'   => 'required|in:pending,active',
        ], [
            'username.regex'  => 'Username hanya boleh huruf, angka, titik, dan underscore.',
            'username.unique' => 'Username sudah digunakan dosen lain.',
            'name.regex'      => 'Nama hanya boleh huruf, spasi, titik, dan tanda hubung.',
            'email.unique'    => 'Email sudah digunakan dosen lain.',
            'password.min'     => 'Password minimal 8 karakter.',
            'password.max'     => 'Password terlalu panjang.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'message'  => 'Validasi gagal',
                'msgField' => $validator->errors(),
            ]);
        }

        $data = [
            'username' => $request->username,
            'name'     => $request->name,
            'email'    => $request->email ?: null,
            'status'   => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'Data dosen berhasil diupdate'
        ]);
    }

    // ======================
    // DELETE POPUP
    // ======================
    public function confirm_ajax($id)
    {
        $user = User::findOrFail($id);
        return view('layoutAdmin.manajemenDosen.confirm', compact('user'));
    }

    // ======================
    // DELETE
    // ======================
    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Dosen berhasil dihapus'
        ]);
    }
}