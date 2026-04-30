<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JawabanSurveiModel;
use App\Models\alumniModel;
use Illuminate\Http\Request;

class MobileAnswerController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'answers' => 'required|array|min:1',
            'answers.*.question_id' => 'required|exists:pertanyaan,pertanyaan_id',
            'answers.*.answer' => 'required',
            'atasan_id' => 'nullable|exists:atasan,atasan_id',
        ]);

        $alumni = alumniModel::where('user_id', $request->user_id)->first();
        if (!$alumni) {
            return response()->json([
                'status' => false,
                'message' => 'Data alumni untuk user ini tidak ditemukan',
            ], 404);
        }

        $atasanId = $request->atasan_id ?: $alumni->atasan_id;
        if (!$atasanId) {
            return response()->json([
                'status' => false,
                'message' => 'Atasan belum tersedia. Lengkapi data atasan terlebih dahulu.',
            ], 422);
        }

        foreach ($request->answers as $item) {
            $answerValue = is_array($item['answer']) ? json_encode($item['answer']) : $item['answer'];

            JawabanSurveiModel::updateOrCreate(
                [
                    'alumni_id' => $alumni->alumni_id,
                    'atasan_id' => $atasanId,
                    'pertanyaan_id' => $item['question_id'],
                ],
                [
                    'jawaban' => $answerValue,
                ]
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Jawaban berhasil disimpan',
        ]);
    }
}

