<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;
    protected $table = 'answers';
    protected $fillable = [
    'alumni_id',
    'question_id'
    ];

    public function answerDetails()
    {
        return $this->hasMany(AnswerDetail::class);
    }
}
