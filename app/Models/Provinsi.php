<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    protected $fillable = ['kode', 'nama'];

    public function kotaKabupaten()
    {
        return $this->hasMany(KotaKabupaten::class);
    }
}
