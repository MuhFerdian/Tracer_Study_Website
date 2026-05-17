<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LowonganPekerjaan extends Model
{
    
    protected $table = 'lowongan_pekerjaan'; 

    // ✅ Tambahkan 'logo' agar field ini bisa diisi saat create/update
    protected $fillable = [
        'posisi',
        'nama_perusahaan',
        'lokasi',
        'gaji',
        'deskripsi',
        'batas_lamaran',
        'kontak',
        'link_lamaran',
        'logo',          // ← Field logo ditambahkan di sini
        'dibuat_oleh',
        'role',
        'aktif'
    ];
}