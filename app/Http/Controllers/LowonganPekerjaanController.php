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
        'kontak' => ['required', 'regex:/^(08)[0-9]{8,12}$/'],
        'batas_lamaran' => ['required', 'date'],
        'link_lamaran' => ['nullable', 'url'],
        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:5120'
    ]);

    $foto = null;

    if ($request->hasFile('foto')) {
        $foto = $request->file('foto')->store('loker', 'public');
    }

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
            'foto' => $foto
        ]);

        return response()->json([
        'success' => true,
        'message' => 'Lowongan berhasil ditambahkan'
    ]);
}

    public function edit($id)
    {
        $lowongan = LowonganPekerjaan::findOrFail($id);
    return response()->json(
        LowonganPekerjaan::findOrFail($id));
}

    public function update(Request $request, $id)
{
    $request->validate([
        'posisi' => 'required',
        'nama_perusahaan' => 'required',
        'kontak' => ['required', 'regex:/^(08)[0-9]{8,12}$/'],
        'batas_lamaran' => ['required', 'date'],
        'link_lamaran' => ['nullable', 'url'],
        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:5120'
    ]);

    $lowongan = LowonganPekerjaan::findOrFail($id);
    $data = $request->except('foto');
    if ($request->hasFile('foto')) {
        // Hapus foto lama jika ada (opsional tapi disarankan)
        if ($lowongan->foto && \Storage::disk('public')->exists($lowongan->foto)) {
            \Storage::disk('public')->delete($lowongan->foto);
        }
        $data['foto'] = $request->file('foto')->store('loker', 'public');
    }

     $lowongan->update($data);

    return response()->json([
        'success' => true,
        'message' => 'Lowongan berhasil diupdate'
    ]);
}

    public function destroy($id)
{
    $lowongan = LowonganPekerjaan::findOrFail($id);
    $lowongan->delete();

    return response()->json([
        'success' => true,
        'message' => 'Lowongan berhasil dihapus'
    ]);
}

    public function showAdmin($id)
{
    $lowongan = LowonganPekerjaan::findOrFail($id);
    // Kita arahkan ke view khusus detail admin
    return view('layoutAdmin.lowongan.show', compact('lowongan'));
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