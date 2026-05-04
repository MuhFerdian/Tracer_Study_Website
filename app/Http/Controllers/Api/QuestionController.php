<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;

class QuestionController extends Controller
{
    //

    public function index()
    {
        $questions = Question::with('options')
            ->orderBy('urutan')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $questions
        ]);
    }
}
