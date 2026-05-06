<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'kode_soal',
        'pertanyaan',
        'type',
        'is_required',
        'urutan',
        'deskripsi',
        'tipe_data',
    ];

    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function details()
    {
        return $this->hasMany(QuestionDetail::class)->orderBy('urutan');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
