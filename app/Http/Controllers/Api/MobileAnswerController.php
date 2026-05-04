<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\AnswerDetail;
use Illuminate\Http\Request;

class MobileAnswerController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'answers' => 'required|array|min:1',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.option_ids' => 'nullable|array',
            'answers.*.value' => 'nullable'
        ]);

        // ambil user login
        $user = auth()->user();

        // ambil alumni berdasarkan user
        $alumni = $user->alumni;

        if (!$alumni) {
            return response()->json([
                'status' => false,
                'message' => 'Data alumni tidak ditemukan'
            ], 404);
        }
        // DEBUG
        if (!$alumni->id) {
            return response()->json([
                'status' => false,
                'message' => 'Alumni ID kosong'
            ], 500);
        }

        foreach ($request->answers as $item) {

            // simpan ke table answers
            $answer = Answer::updateOrCreate(
                [
                    'alumni_id' => $alumni->id,
                    'question_id' => $item['question_id'],
                ],
                []
            );

            // hapus detail lama
            AnswerDetail::where('answer_id', $answer->id)->delete();

            // =========================
            // MULTIPLE / SINGLE OPTION
            // =========================
            if (!empty($item['option_ids'])) {
                foreach ($item['option_ids'] as $optId) {
                    AnswerDetail::create([
                        'answer_id' => $answer->id,
                        'option_id' => $optId,
                    ]);
                }
            }

            // =========================
            // TEXT / SCALE
            // =========================
            if (!empty($item['value'])) {
                AnswerDetail::create([
                    'answer_id' => $answer->id,
                    'value' => $item['value'],
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Jawaban berhasil disimpan'
        ]);
    }
}