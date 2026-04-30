<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PertanyaanModel extends Model
{
    protected $table = 'pertanyaan';
    protected $primaryKey = 'pertanyaan_id';
    public $timestamps = true;

    protected $fillable = [
        'kode_soal',
        'question_text',
        'type',
        'options',
        'urutan'
    ];

    protected $casts = [
        'options' => 'array'
    ];

    public function jawaban()
    {
        return $this->hasMany(JawabanSurveiModel::class, 'pertanyaan_id', 'pertanyaan_id');
    }
}
