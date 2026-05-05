<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
        $request->validate([
            'username' => 'required|unique:users,username',
            'name' => 'required|min:3',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|min:6',
            'status' => 'required|in:pending,active',
        ]);

        $roleId = $this->getDosenRoleId();

        if (!$roleId) {
            return response()->json([
                'status' => false,
                'message' => 'Role dosen tidak ditemukan'
            ]);
        }

        User::create([
            'role_id' => $roleId,
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => $request->status,
        ]);

        return response()->json([
            'status' => true,
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
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|unique:users,username,' . $user->id,
            'name' => 'required|min:3',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'status' => 'required|in:pending,active',
        ]);

        $data = [
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'status' => true,
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