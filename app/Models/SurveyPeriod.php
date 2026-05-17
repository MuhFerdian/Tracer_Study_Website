<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyPeriod extends Model
{
    use HasFactory;

    protected $table = 'survey_periods';

    protected $fillable = [
        'nama',
        'tahun',
        'tanggal_buka',
        'tanggal_tutup',
        'status',
        'deskripsi',
    ];

    protected $casts = [
        'tanggal_buka'  => 'date:d-m-Y',
        'tanggal_tutup' => 'date:d-m-Y',
        'tahun'         => 'integer',
    ];

    // =============================================
    // Scope: periode yang sedang aktif
    // =============================================
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // =============================================
    // Helper static: ambil periode aktif saat ini
    // =============================================
    public static function getAktif(): ?self
    {
        return static::aktif()->latest()->first();
    }

    // =============================================
    // Relasi ke jawaban
    // =============================================
    public function answers()
    {
        return $this->hasMany(Answer::class, 'survey_period_id');
    }

    // =============================================
    // Accessor: label untuk dropdown
    // =============================================
    public function getLabelAttribute(): string
    {
        return $this->nama . ' (' . $this->tahun . ')';
    }
}
