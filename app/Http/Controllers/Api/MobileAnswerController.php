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
            'answers'                  => 'required|array|min:1',
            'answers.*.question_id'    => 'required|exists:questions,id',
            'answers.*.value'          => 'nullable',
        ]);

        $user   = auth()->user();
        $alumni = $user->alumni;

        if (!$alumni) {
            return response()->json([
                'status'  => false,
                'message' => 'Data alumni tidak ditemukan',
            ], 404);
        }

        foreach ($request->answers as $item) {
            $value = $item['value'] ?? null;

            // Skip jika value kosong / null
            if ($value === null || $value === '' || $value === '[]') {
                continue;
            }

            // Simpan / update header jawaban
            $answer = Answer::updateOrCreate(
                [
                    'alumni_id'   => $alumni->id,
                    'question_id' => $item['question_id'],
                ],
                []
            );

            // Hapus detail lama sebelum simpan yang baru
            AnswerDetail::where('answer_id', $answer->id)->delete();

            // Simpan value langsung (bisa berupa string, angka, atau JSON array)
            AnswerDetail::create([
                'answer_id' => $answer->id,
                'option_id' => null,
                'value'     => is_array($value) ? json_encode($value) : (string) $value,
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Jawaban berhasil disimpan',
        ]);
    }
}