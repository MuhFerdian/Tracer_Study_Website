<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MobileResultController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $alumni = DB::table('alumni')
            ->where('user_id', $user->id)
            ->first();

        if (!$alumni) {
            return response()->json([
                'status' => false,
                'message' => 'Alumni tidak ditemukan'
            ], 404);
        }

        $results = DB::table('answers')
            ->join('questions', 'answers.question_id', '=', 'questions.id')
            ->leftJoin('answer_details', 'answers.id', '=', 'answer_details.answer_id')
            ->leftJoin('question_options', 'answer_details.option_id', '=', 'question_options.id')
            ->where('answers.alumni_id', $alumni->id)
            ->select(
                'questions.id as question_id',
                'questions.pertanyaan',
                'questions.type',
                'question_options.label',
                'answer_details.value'
            )
            ->get()
            ->groupBy('question_id')
            ->map(function ($items) {
                return [
                    'question' => $items[0]->pertanyaan,
                    'type' => $items[0]->type,
                    'answers' => $items->pluck('label')->filter()->values()
                ];
            })
            ->values();

        return response()->json([
            'status' => true,
            'data' => $results
        ]);
    }
}