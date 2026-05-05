<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswerDetail extends Model
{
    use HasFactory;
    protected $table = 'answer_details';
    protected $fillable = [
    'answer_id',
    'option_id',
    'value'
    ];
    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    public function option()
    {
        return $this->belongsTo(QuestionOption::class, 'option_id');
    }
}
