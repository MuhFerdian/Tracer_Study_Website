<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'item_label',
        'field_code_a',
        'field_code_b',
        'urutan'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
