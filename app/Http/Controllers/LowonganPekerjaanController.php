<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LowonganPekerjaan;
use Illuminate\Support\Facades\Storage;

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
            'posisi' => 'required|string|max:255',
            'nama_perusahaan' => 'required|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'gaji' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'batas_lamaran' => 'nullable|date',
            'kontak' => 'nullable|string|max:255',
            'link_lamaran' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->only([
            'posisi', 'nama_perusahaan', 'lokasi', 'gaji', 
            'deskripsi', 'batas_lamaran', 'kontak', 'link_lamaran'
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $data['dibuat_oleh'] = auth()->id();
        $data['role'] = auth()->user()->role->role_nama ?? 'Dosen';
        $data['aktif'] = true;

        LowonganPekerjaan::create($data);

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

        $request->validate([
            'posisi' => 'required|string|max:255',
            'nama_perusahaan' => 'required|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'gaji' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'batas_lamaran' => 'nullable|date',
            'kontak' => 'nullable|string|max:255',
            'link_lamaran' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->only([
            'posisi', 'nama_perusahaan', 'lokasi', 'gaji', 
            'deskripsi', 'batas_lamaran', 'kontak', 'link_lamaran'
        ]);

        if ($request->filled('hapus_logo') && $request->hapus_logo == '1') {
            if ($lowongan->logo && Storage::disk('public')->exists($lowongan->logo)) {
                Storage::disk('public')->delete($lowongan->logo);
            }
            $data['logo'] = null;
        }
        elseif ($request->hasFile('logo')) {
            if ($lowongan->logo && Storage::disk('public')->exists($lowongan->logo)) {
                Storage::disk('public')->delete($lowongan->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $lowongan->update($data);

        return redirect('/admin/lowongan-pekerjaan')
            ->with('success', 'Lowongan berhasil diupdate');
    }

    public function destroy($id)
    {
        $lowongan = LowonganPekerjaan::findOrFail($id);

        if ($lowongan->logo && Storage::disk('public')->exists($lowongan->logo)) {
            Storage::disk('public')->delete($lowongan->logo);
        }

        $lowongan->delete();

        return back()->with('success', 'Lowongan berhasil dihapus');
    }

    // =======================
    // ✅ LOWONGAN PUBLIC - DENGAN SEARCH STABIL
    // =======================
    public function publicIndex(Request $request)
    {
        // ✅ Ambil keyword dari request, bersihkan spasi
        $search = trim($request->input('search', ''));
        
        // ✅ Query dasar: hanya lowongan aktif
        $query = LowonganPekerjaan::where('aktif', true);
        
        // ✅ Jika ada keyword, lakukan filter
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('posisi', 'like', '%'.$search.'%')
                  ->orWhere('nama_perusahaan', 'like', '%'.$search.'%')
                  ->orWhere('deskripsi', 'like', '%'.$search.'%')
                  ->orWhere('lokasi', 'like', '%'.$search.'%');
            });
        }
        
        // ✅ Pagination: 6 item per halaman, pertahankan query string
        $lowongan = $query->latest()->paginate(6)->withQueryString();
        
        // ✅ Return view dengan data
        return view('layoutLandingPage.lowongan.index', compact('lowongan', 'search'));
    }

    public function show($id)
    {
        $lowongan = LowonganPekerjaan::findOrFail($id);
        return view('layoutLandingPage.lowongan.detail', compact('lowongan'));
    }
}