<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LowonganPekerjaan;

class LowonganPekerjaanController extends Controller
{
    public function index()
    {
        $lowongan = LowonganPekerjaan::latest()->get();

        return view('layoutAdmin.lowongan.index', compact('lowongan'));
    }

    public function list()
    {
        $data = LowonganPekerjaan::latest()->get();

        return response()->json($data);
    }

    public function create()
    {
        return view('layoutAdmin.lowongan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'posisi' => 'required',
            'nama_perusahaan' => 'required',
        ]);

        LowonganPekerjaan::create([
            'posisi' => $request->posisi,
            'nama_perusahaan' => $request->nama_perusahaan,
            'lokasi' => $request->lokasi,
            'gaji' => $request->gaji,
            'deskripsi' => $request->deskripsi,
            'batas_lamaran' => $request->batas_lamaran,
            'kontak' => $request->kontak,
            'link_lamaran' => $request->link_lamaran,
            'dibuat_oleh' => auth()->id(),
            'role' => auth()->user()->role->role_nama ?? 'Dosen',
            'aktif' => true,
        ]);

        return redirect('/admin/lowongan-pekerjaan')
            ->with('success', 'Lowongan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $lowongan = LowonganPekerjaan::findOrFail($id);

        return view('layoutAdmin.lowongan.edit', compact('lowongan'));
    }

    public function update(Request $request, $id)
    {
        $lowongan = LowonganPekerjaan::findOrFail($id);

        $lowongan->update($request->all());

        return redirect('/admin/lowongan-pekerjaan')
            ->with('success', 'Lowongan berhasil diupdate');
    }

    public function destroy($id)
    {
        $lowongan = LowonganPekerjaan::findOrFail($id);

        $lowongan->delete();

        return back()->with('success', 'Lowongan berhasil dihapus');
    }

// =======================
// LOWONGAN PUBLIC
// =======================
    public function publicIndex()
{
    $lowongan = LowonganPekerjaan::where('aktif', true)
        ->latest()
        ->paginate(6);

    return view('layoutLandingPage.lowongan.index', compact('lowongan'));
}

public function show($id)
{
    $lowongan = LowonganPekerjaan::findOrFail($id);

    return view('layoutLandingPage.lowongan.detail', compact('lowongan'));
}
}