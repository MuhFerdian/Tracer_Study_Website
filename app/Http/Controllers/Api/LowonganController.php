<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LowonganPekerjaan;
use App\Models\Notification;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Support\Facades\Validator;

class LowonganController extends Controller
{
    // =============================================
    // GET LIST LOWONGAN
    // Query params: ?search=, ?per_page=, ?page=
    // =============================================
    public function index(Request $request)
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

        $perPage   = (int) $request->get('per_page', 10);
        $perPage   = min(max($perPage, 1), 50);
        $paginated = $query->paginate($perPage);

        $items = collect($paginated->items())
            ->map(fn($item) => $this->formatLowongan($item));

        return response()->json([
            'status'  => true,
            'message' => 'Data lowongan berhasil diambil',
            'data'    => $items,
            'meta'    => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
            ],
        ]);
    }

    // =============================================
    // GET DETAIL LOWONGAN
    // =============================================
    public function show($id)
    {
        $lowongan = LowonganPekerjaan::where('aktif', true)->find($id);

        if (!$lowongan) {
            return response()->json([
                'status'  => false,
                'message' => 'Lowongan tidak ditemukan atau sudah tidak aktif',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail lowongan berhasil diambil',
            'data'    => $this->formatLowongan($lowongan),
        ]);
    }

    // =============================================
    // TAMBAH LOWONGAN
    // =============================================
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'posisi'          => 'required|string|max:255',
            'nama_perusahaan' => 'required|string|max:255',
            'lokasi'          => 'nullable|string|max:255',
            'gaji'            => 'nullable|string|max:100',
            'deskripsi'       => 'nullable|string|max:5000',
            'batas_lamaran'   => 'nullable|date|after_or_equal:today',
            'kontak'          => ['nullable', 'regex:/^(08)[0-9]{8,12}$/'],
            'link_lamaran'    => 'nullable|url|max:500',
            'dibuat_oleh'     => 'nullable|integer|exists:users,id',
            'role'            => 'nullable|in:admin,dosen,alumni',

            // FOTO
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

        ], [
            'posisi.required'              => 'Posisi pekerjaan wajib diisi.',
            'nama_perusahaan.required'     => 'Nama perusahaan wajib diisi.',
            'batas_lamaran.date'           => 'Format tanggal batas lamaran tidak valid.',
            'batas_lamaran.after_or_equal' => 'Batas lamaran tidak boleh kurang dari hari ini.',
            'kontak.regex'                 => 'Nomor kontak harus diawali 08 dan terdiri dari 10–14 digit.',
            'link_lamaran.url'             => 'Link lamaran harus berupa URL yang valid.',
            'dibuat_oleh.exists'           => 'User pembuat tidak ditemukan.',
            'role.in'                      => 'Role harus salah satu dari: admin, dosen, alumni.',

            // FOTO
            'foto.image'                   => 'File harus berupa gambar.',
            'foto.mimes'                   => 'Foto harus berformat jpg, jpeg, atau png.',
            'foto.max'                     => 'Ukuran foto maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // =============================================
        // UPLOAD FOTO
        // =============================================
        $fotoPath = null;

        if ($request->hasFile('foto')) {

            $file = $request->file('foto');

            // Nama file unik
            $namaFile = time() . '_' . $file->getClientOriginalName();

            // Folder tujuan
            $tujuanPath = public_path(
                'startbootstrap-sb-admin-gh-pages/assets/foto_loker'
            );

            // Pindahkan file
            $file->move($tujuanPath, $namaFile);

            // Simpan path ke database
            $fotoPath =
                'startbootstrap-sb-admin-gh-pages/assets/foto_loker/' . $namaFile;
        }

        // =============================================
        // SIMPAN LOWONGAN
        // =============================================
        $lowongan = LowonganPekerjaan::create([
            'posisi'          => $request->posisi,
            'nama_perusahaan' => $request->nama_perusahaan,
            'lokasi'          => $request->lokasi,
            'gaji'            => $request->gaji,
            'deskripsi'       => $request->deskripsi,
            'batas_lamaran'   => $request->batas_lamaran,
            'kontak'          => $request->kontak,
            'link_lamaran'    => $request->link_lamaran,
            'dibuat_oleh'     => $request->dibuat_oleh,
            'role'            => $request->role ?? 'alumni',
            'aktif'           => true,

            // FOTO
            'foto'            => $fotoPath,
        ]);

        // =============================================
        // PUSH NOTIFIKASI
        // =============================================
        $users = User::whereNotNull('fcm_token')->get();

        $fcm = new FcmService();

        foreach ($users as $user) {

            $notif = Notification::create([
                'user_id' => $user->id,
                'title'   => 'Lowongan Baru 🔥',
                'body'    => $lowongan->posisi . ' di ' . $lowongan->nama_perusahaan,
                'type'    => 'lowongan',
                'is_read' => 0,
            ]);

            $fcm->sendNotification(
                $user->fcm_token,
                $notif->title,
                $notif->body
            );
        }

        return response()->json([
            'status'  => true,
            'message' => 'Lowongan berhasil ditambahkan dan notifikasi dikirim',
            'data'    => $this->formatLowongan($lowongan),
        ], 201);
    }

    // =============================================
    // FORMAT RESPONSE
    // =============================================
    private function formatLowongan(LowonganPekerjaan $item): array
    {
        return [
            'id'              => $item->id,
            'posisi'          => $item->posisi,
            'nama_perusahaan' => $item->nama_perusahaan,
            'lokasi'          => $item->lokasi,
            'gaji'            => $item->gaji,
            'deskripsi'       => $item->deskripsi,
            'batas_lamaran'   => $item->batas_lamaran,
            'kontak'          => $item->kontak,
            'link_lamaran'    => $item->link_lamaran,
            'dibuat_oleh'     => $item->dibuat_oleh,
            'role'            => $item->role,
            'aktif'           => (bool) $item->aktif,

            // FOTO URL
            'foto_url'        => $item->foto
                ? asset($item->foto)
                : null,

            'created_at'      => $item->created_at,
            'updated_at'      => $item->updated_at,
        ];
    }
}