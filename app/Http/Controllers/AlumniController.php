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
            'email' => 'nullable|email|max:255',
            'tahun_lulus' => 'nullable|integer|min:1900|max:2100',
            'status_pekerjaan' => 'nullable|string|max:255',
            'nama_instansi' => 'nullable|string|max:255',
            'posisi' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $alumni = alumniModel::findOrFail($id);

        // UPLOAD FOTO
        if ($request->hasFile('image')) {

            // hapus foto lama jika ada
            if ($alumni->image && Storage::disk('public')->exists($alumni->image)) {
                Storage::disk('public')->delete($alumni->image);
            }

            // simpan foto baru
            $path = $request->file('image')->store('alumni', 'public');

            $validated['image'] = $path;
        }

        $alumni->fill($validated);
        $alumni->save();

        Auth::guard('alumni')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Data alumni berhasil diperbarui']);
    }
}
