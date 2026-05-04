<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;

class MobileQuestionController extends Controller
{
    public function index(Request $request)
    {
        // ambil semua pertanyaan + options
        $questions = Question::with('options')
            ->orderBy('urutan')
            ->get();

        $result = $questions->map(function ($q) {
            return [
                'id' => $q->id,
                'kode' => $q->kode,
                'question_text' => $q->pertanyaan,
                'type' => $q->type, // single, multiple, text, scale
                'is_required' => $q->is_required,

                'options' => $q->options->map(function ($opt) {
                    return [
                        'id' => $opt->id,
                        'label' => $opt->label,
                        'value' => $opt->value,
                    ];
                })
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $result
        ]);
    }
}