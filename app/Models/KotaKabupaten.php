<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KotaKabupaten extends Model
{
    protected $table = 'kota_kabupaten';
    protected $fillable = ['provinsi_id', 'kode', 'nama'];

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class);
    }
}
