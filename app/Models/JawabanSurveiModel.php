<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JawabanSurveiModel extends Model
{
    use HasFactory;

    protected $table = 'jawaban';
    protected $primaryKey = 'jawaban_id';
    public $timestamps = true;

    protected $fillable = [
        'pertanyaan_id',
        'alumni_id',
        'jawaban',
    ];
}
