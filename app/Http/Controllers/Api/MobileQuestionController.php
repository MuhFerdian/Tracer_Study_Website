<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JawabanSurveiModel;
use App\Models\PertanyaanModel;
use App\Models\alumniModel;
use Illuminate\Http\Request;

class MobileQuestionController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $alumni = alumniModel::where('user_id', $request->user_id)->first();
        if (!$alumni) {
            return response()->json([
                'status' => false,
                'message' => 'Data alumni untuk user ini tidak ditemukan',
            ], 404);
        }

        $questions = PertanyaanModel::orderBy('pertanyaan_id')->get();

        $answers = JawabanSurveiModel::where('alumni_id', $alumni->alumni_id)
            ->orderByDesc('updated_at')
            ->get()
            ->unique('pertanyaan_id')
            ->keyBy('pertanyaan_id');

        $result = $questions->map(function ($q) use ($answers) {
            $answer = $answers->get($q->pertanyaan_id);

            return [
                'id' => $q->pertanyaan_id,
                'kode_soal' => 'Q' . $q->pertanyaan_id,
                'question_text' => $q->pertanyaan,
                'type' => 'radio',
                'options' => ['Kurang', 'Cukup', 'Baik', 'Sangat Baik'],
                'answer' => $answer ? $answer->jawaban : null,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $result,
        ]);
    }
}

