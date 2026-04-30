<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\userModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ManajemenDosenController extends Controller
{
    public function index()
    {
        $dosenRoleId = DB::table('role')->where('role_nama', 'Dosen')->value('role_id');
        if (!$dosenRoleId) {
            return back()->with('error', 'Role Dosen tidak ditemukan.');
        }
        $dosens = userModel::where('role_id', $dosenRoleId)->get();
        return view('layoutAdmin.manajemenDosen.index', compact('dosens'));
    }

    public function create()
    {
        return view('layoutAdmin.manajemenDosen.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'name' => 'required|min:3',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|min:6',
            'status' => 'required|in:pending,active',
        ]);

        $dosenRoleId = DB::table('role')->where('role_nama', 'Dosen')->value('role_id');
        if (!$dosenRoleId) {
            return back()->with('error', 'Role Dosen tidak ditemukan.');
        }

        userModel::create([
            'role_id' => $dosenRoleId,
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => $request->status,
        ]);

        return redirect(url('/admin/manajemen-dosen'))->with('success', 'Dosen berhasil dibuat.');
    }

    public function edit($id)
    {
        $user = userModel::findOrFail($id);
        return view('layoutAdmin.manajemenDosen.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = userModel::findOrFail($id);
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

        return redirect(url('/admin/manajemen-dosen'))->with('success', 'Data dosen diperbarui.');
    }

    public function destroy($id)
    {
        $user = userModel::findOrFail($id);
        $user->delete();
        return redirect(url('/admin/manajemen-dosen'))->with('success', 'Dosen dihapus.');
    }
}
