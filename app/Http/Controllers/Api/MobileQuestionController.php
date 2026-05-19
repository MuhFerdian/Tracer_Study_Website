<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;

class MobileQuestionController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user_id;

        $user = \App\Models\User::find($userId);

        if (!$user || !$user->alumni) {
            return response()->json([
                'status' => false,
                'message' => 'Alumni tidak ditemukan'
            ]);
        }

        $alumniId = $user->alumni->id;

        // Ambil semua jawaban alumni
        $savedAnswers = Answer::with('answerDetails')
            ->where('alumni_id', $alumniId)
            ->get()
            ->keyBy('question_id');

        // ambil semua pertanyaan + options
        $questions = Question::with([
                'options' => function ($query) {
                    $query->orderBy('urutan');
                },
                'details'
            ])
            ->orderBy('urutan')
            ->get();

        $result = $questions->map(function ($q) use ($savedAnswers) {

            $savedAnswer = null;

            // cek apakah ada jawaban tersimpan
            if (isset($savedAnswers[$q->id])) {

                $detail = $savedAnswers[$q->id]
                    ->answerDetails
                    ->first();

                if ($detail) {
                    $savedAnswer = $detail->value;
                }
            }

            return [
                'id'            => $q->id,
                'kode'          => $q->kode_soal,
                'kode_soal'     => $q->kode_soal,
                'question_text' => $q->pertanyaan,
                'hint'          => $q->hint,
                'type'          => $q->type,
                'tipe_data'     => $q->tipe_data,
                'is_required'   => $q->is_required,

                // TAMBAHAN INI
                'saved_answer' => $savedAnswer,

                'options' => $q->options->map(function ($opt) {
                    return [
                        'id'    => $opt->id,
                        'label' => $opt->label,
                        'value' => $opt->value,
                    ];
                }),

                'details' => $q->details->map(function ($detail) {
                    return [
                        'id'     => $detail->id,
                        'label'  => $detail->item_label,
                        'urutan' => $detail->urutan,
                    ];
                }),
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $result
        ]);
    }
}