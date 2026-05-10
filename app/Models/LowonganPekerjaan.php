<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'aktif'
    ];
}