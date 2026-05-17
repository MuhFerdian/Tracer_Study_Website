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
        'question_id',
        'survey_period_id',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function surveyPeriod()
    {
        return $this->belongsTo(SurveyPeriod::class, 'survey_period_id');
    }

    public function answerDetails()
    {
        return $this->hasMany(AnswerDetail::class);
    }
}
