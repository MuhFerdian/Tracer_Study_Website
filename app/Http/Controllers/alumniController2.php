<?php

namespace App\Http\Controllers;

use App\Models\alumniModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AlumniController extends Controller
{
    public function index($id)
    {
        $alumni = alumniModel::findOrFail($id);

        return view('layoutAlumni.index', compact('alumni'));
    }

    public function list($id)
    {
        $alumni = alumniModel::findOrFail($id);

        return response()->json([
            'alumni' => $alumni,
        ]);
    }

    public function byKategori($kategori_profesi_id)
    {
        return response()->json([]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nim' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'prodi' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|regex:/^[a-zA-Z0-9._%+\-]+@gmail\.com$/i',
            'tahun_lulus' => 'nullable|integer|min:1900|max:2100',
            'status_pekerjaan' => 'nullable|string|max:255',
            'nama_instansi' => 'nullable|string|max:255',
            'posisi' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $alumni = alumniModel::findOrFail($id);

        // =========================
        // UPLOAD FOTO
        // =========================
        if ($request->hasFile('image')) {

            // hapus foto lama
            if (
                $alumni->image &&
                Storage::disk('public')->exists($alumni->image)
            ) {
                Storage::disk('public')->delete($alumni->image);
            }

            // upload foto baru
            $path = $request->file('image')->store('alumni', 'public');

            $validated['image'] = $path;
        }

        $alumni->fill($validated);
        $alumni->save();

        Auth::guard('alumni')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Data alumni berhasil diperbarui'
        ]);
    }

    // ======================================================
    // UPDATE PROFILE FLUTTER
    // ======================================================
    public function updateProfile(Request $request)
    {
        $request->validate([
            'nim' => 'required',
            'nama' => 'required',
            'email' => 'required|email',
            'no_hp' => 'required',
            'prodi' => 'required',
            'angkatan' => 'required',
            'tahun_lulus' => 'required',
            'alamat' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $alumni = alumniModel::where('nim', $request->nim)->first();

        if (!$alumni) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        // ==============================
        // HAPUS FOTO PROFIL
        // ==============================
        if ($request->remove_image == '1') {
            if ($alumni->image && Storage::disk('public')->exists($alumni->image)) {
                Storage::disk('public')->delete($alumni->image);
            }
            $alumni->image = null;
        }

        // ==============================
        // UPLOAD FOTO BARU
        // ==============================
        if ($request->hasFile('image')) {

            // hapus lama (kalau belum dihapus via remove_image)
            if ($alumni->image && Storage::disk('public')->exists($alumni->image)) {
                Storage::disk('public')->delete($alumni->image);
            }

            $path = $request->file('image')->store('profile', 'public');
            $alumni->image = $path;
        }

        // ==============================
        // UPDATE DATA
        // ==============================
        $alumni->nama = $request->nama;
        $alumni->no_hp = $request->no_hp;
        $alumni->email = $request->email;
        $alumni->prodi = $request->prodi;
        $alumni->angkatan = $request->angkatan;
        $alumni->tahun_lulus = $request->tahun_lulus;
        $alumni->alamat = $request->alamat;
        $alumni->tempat_lahir = $request->tempat_lahir;
        $alumni->tanggal_lahir = $request->tanggal_lahir;

        $alumni->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile berhasil diupdate',
            'data' => $alumni
        ]);
    }
}