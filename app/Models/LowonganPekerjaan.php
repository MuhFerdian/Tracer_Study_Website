<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LowonganPekerjaan extends Model
{
    protected $table = 'lowongan_pekerjaan';

    protected $fillable = [
        'posisi',
        'nama_perusahaan',
        'lokasi',
        'gaji',
        'deskripsi',
        'batas_lamaran',
        'kontak',
        'link_lamaran',
        'dibuat_oleh',
        'role',
        'aktif',
        'foto'
    ];

    // ── Lowongan dibuat oleh satu User ──
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh', 'id');
    }
}