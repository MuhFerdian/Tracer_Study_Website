<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LowonganPekerjaan;
use App\Models\Notification;
use App\Models\User;
use App\Services\FcmService;

class LowonganController extends Controller
{
    // =========================
    // GET LOWONGAN
    // =========================
    public function index()
    {
        $data = LowonganPekerjaan::where('aktif', true)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    // =========================
    // TAMBAH LOWONGAN
    // =========================
public function store(Request $request)
{
    $lowongan = LowonganPekerjaan::create([
        'posisi' => $request->posisi,
        'nama_perusahaan' => $request->nama_perusahaan,
        'lokasi' => $request->lokasi,
        'gaji' => $request->gaji,
        'deskripsi' => $request->deskripsi,
        'batas_lamaran' => $request->batas_lamaran,
        'kontak' => $request->kontak,
        'link_lamaran' => $request->link_lamaran,
        'dibuat_oleh' => $request->dibuat_oleh,
        'role' => $request->role ?? 'alumni',
        'aktif' => true,
    ]);

    // ambil user yang valid saja
    $users = User::whereNotNull('fcm_token')->get();

    $fcm = new FcmService();

    foreach ($users as $user) {

        $notif = Notification::create([
            'user_id' => $user->id,
            'title' => 'Lowongan Baru 🔥',
            'body' => $lowongan->posisi . ' di ' . $lowongan->nama_perusahaan,
            'type' => 'lowongan',
            'is_read' => 0,
        ]);

        $fcm->sendNotification(
            $user->fcm_token,
            $notif->title,
            $notif->body
        );
    }

    return response()->json([
        'status' => true,
        'message' => 'Lowongan + notifikasi berhasil dikirim',
        'data' => $lowongan
    ]);
}
}