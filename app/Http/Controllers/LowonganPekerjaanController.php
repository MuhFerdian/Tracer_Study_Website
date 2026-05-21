<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LowonganPekerjaan;
use App\Models\User;
use App\Services\FcmService;

class LowonganPekerjaanController extends Controller
{
    // =============================================
    // SHARED VALIDATION RULES
    // =============================================
    private function validationRules(bool $isUpdate = false): array
    {
        return [
            'posisi'          => 'required|string|max:255',
            'nama_perusahaan' => 'required|string|max:255',
            'lokasi'          => 'nullable|string|max:255',
            'gaji'            => 'nullable|string|max:100',
            'deskripsi'       => 'nullable|string|max:5000',
            'batas_lamaran'   => ['required', 'date', $isUpdate ? 'nullable' : 'after_or_equal:today'],
            'kontak'          => ['required', 'regex:/^(08)[0-9]{8,12}$/'],
            'link_lamaran'    => ['nullable', 'url', 'max:500'],
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ];
    }

    private function validationMessages(): array
    {
        return [
            'posisi.required'          => 'Posisi pekerjaan wajib diisi.',
            'nama_perusahaan.required' => 'Nama perusahaan wajib diisi.',
            'batas_lamaran.required'   => 'Batas lamaran wajib diisi.',
            'batas_lamaran.date'       => 'Format tanggal batas lamaran tidak valid.',
            'batas_lamaran.after_or_equal' => 'Batas lamaran tidak boleh kurang dari hari ini.',
            'kontak.required'          => 'Nomor kontak wajib diisi.',
            'kontak.regex'             => 'Nomor kontak harus diawali 08 dan terdiri dari 10-14 digit.',
            'link_lamaran.url'         => 'Link lamaran harus berupa URL yang valid (contoh: https://...).',
            'foto.image'               => 'File foto harus berupa gambar.',
            'foto.mimes'               => 'Format foto harus JPG, JPEG, atau PNG.',
            'foto.max'                 => 'Ukuran foto maksimal 5 MB.',
        ];
    }

    // =============================================
    // ADMIN CRUD
    // =============================================
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

    public function store(Request $request, FcmService $fcm)
    {
        $request->validate([
            'posisi'          => 'required',
            'nama_perusahaan' => 'required',
            'kontak'          => ['required', 'regex:/^(08)[0-9]{8,12}$/'],
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ], [
            'kontak.regex' => 'Nomor kontak harus diawali 08 dan terdiri dari 10-14 digit.',
            'foto.image'   => 'File foto harus berupa gambar.',
            'foto.mimes'   => 'Format foto harus JPG, JPEG, atau PNG.',
            'foto.max'     => 'Ukuran foto maksimal 5 MB.',
        ]);

        $foto = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('foto_loker', 'public');
            $foto = $path;
        }

        $lowongan = LowonganPekerjaan::create([
            'posisi'          => $request->posisi,
            'nama_perusahaan' => $request->nama_perusahaan,
            'lokasi'          => $request->lokasi,
            'gaji'            => $request->gaji,
            'deskripsi'       => $request->deskripsi,
            'batas_lamaran'   => $request->batas_lamaran,
            'kontak'          => $request->kontak,
            'link_lamaran'    => $request->link_lamaran,
            'dibuat_oleh'     => auth()->id(),
            'role'            => auth()->user()->role->role_nama ?? 'Dosen',
            'aktif'           => true,
            'foto'            => $foto,
        ]);

        // ambil semua alumni
        $users = User::whereHas('role', function ($q) {
            $q->where('role_nama', 'Alumni');
        })->get();

        // kirim notif
        foreach ($users as $user) {

            $fcm->sendToUser(
                $user->id,
                "Lowongan Baru 🎉",
                "Lowongan {$lowongan->posisi} telah tersedia",
                // [
                //     'type' => 'lowongan',
                //     'lowongan_id' => $lowongan->id
                // ]
            );
        }

        return redirect('/admin/lowongan-pekerjaan')
            ->with('success', 'Lowongan berhasil ditambahkan');
    }

    public function edit($id)
    {
        return response()->json(LowonganPekerjaan::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        // Saat update, batas_lamaran boleh sama dengan tanggal lama (nullable after_or_equal)
        $rules = $this->validationRules(true);
        $rules['batas_lamaran'] = ['required', 'date'];

        $request->validate($rules, $this->validationMessages());

        $lowongan = LowonganPekerjaan::findOrFail($id);
        $data     = $request->except(['foto', '_method', '_token']);

        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($lowongan->foto) {
                if (str_starts_with($lowongan->foto, 'startbootstrap')) {
                    // Path lama: file ada di public/
                    $oldPath = public_path($lowongan->foto);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                } else {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($lowongan->foto);
                }
            }
            $path         = $request->file('foto')->store('foto_loker', 'public');
            $data['foto'] = $path;
        }

        $lowongan->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Lowongan berhasil diupdate',
        ]);
    }

    public function destroy($id)
    {
        $lowongan = LowonganPekerjaan::findOrFail($id);

        // Hapus foto sebelum delete record
        if ($lowongan->foto) {
            if (str_starts_with($lowongan->foto, 'startbootstrap')) {
                // Path lama: file ada di public/
                $oldPath = public_path($lowongan->foto);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            } else {
                // Path baru: file ada di storage/app/public/
                \Illuminate\Support\Facades\Storage::disk('public')->delete($lowongan->foto);
            }
        }

        $lowongan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lowongan berhasil dihapus',
        ]);
    }

    public function showAdmin($id)
    {
        $lowongan = LowonganPekerjaan::findOrFail($id);

        return view('layoutAdmin.lowongan.show', compact('lowongan'));
    }

    // =============================================
    // LOWONGAN PUBLIC (Landing Page)
    // =============================================
    public function publicIndex(Request $request)
    {
        $query = LowonganPekerjaan::where('aktif', true)->latest();

        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('posisi', 'like', "%{$keyword}%")
                    ->orWhere('nama_perusahaan', 'like', "%{$keyword}%")
                    ->orWhere('lokasi', 'like', "%{$keyword}%");
            });
        }

        $lowongan = $query->paginate(6)->withQueryString();

        return view('layoutLandingPage.lowongan.index', compact('lowongan'));
    }

    public function show($id)
    {
        $lowongan = LowonganPekerjaan::where('aktif', true)->findOrFail($id);

        return view('layoutLandingPage.lowongan.detail', compact('lowongan'));
    }
}
