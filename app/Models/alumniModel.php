<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;

class alumniModel extends Authenticatable
{
    use HasFactory;

    protected $table = 'alumni';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'nim',
        'nama',
        'prodi',
        'no_hp',
        'email',
        'alamat',
        'tahun_lulus',
        'status_pekerjaan',
        'nama_instansi',
        'posisi',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'tahun_lulus' => 'integer',
    ];

    public function getAlumniIdAttribute(): int
    {
        return (int) $this->id;
    }

    public function getNamaAlumniAttribute(): ?string
    {
        return $this->nama ?? null;
    }

    public function setNamaAlumniAttribute($value): void
    {
        $this->attributes['nama'] = $value;
    }

    public function getTanggalLulusAttribute()
    {
        $year = $this->tahun_lulus;

        if (empty($year)) {
            return null;
        }

        return Carbon::createFromDate((int) $year, 12, 31);
    }

    public function setTanggalLulusAttribute($value): void
    {
        if (empty($value)) {
            $this->attributes['tahun_lulus'] = null;
            return;
        }

        if (is_numeric($value) && strlen((string) $value) === 4) {
            $this->attributes['tahun_lulus'] = (int) $value;
            return;
        }

        $this->attributes['tahun_lulus'] = (int) Carbon::parse($value)->format('Y');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
