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
        $questions = Question::with(['options' => function ($query) {
                $query->orderBy('urutan');
            }, 'details'])
            ->orderBy('urutan')
            ->get();

        $result = $questions->map(function ($q) {
            return [
                'id'          => $q->id,
                'kode'        => $q->kode_soal,
                'kode_soal'   => $q->kode_soal,
                'group_label' => $q->group_label,
                'question_text' => $q->pertanyaan,
                'hint'        => $q->hint,
                'type'        => $q->type,
                'tipe_data'   => $q->tipe_data,
                'is_required' => $q->is_required,

                'options' => $q->options->map(function ($opt) {
                    return [
                        'id'    => $opt->id,
                        'label' => $opt->label,
                        'value' => $opt->value,
                    ];
                }),

                'details' => $q->details->map(function ($detail) {
                    return [
                        'id'           => $detail->id,
                        'label'        => $detail->item_label,
                        'field_code_a' => $detail->field_code_a,
                        'field_code_b' => $detail->field_code_b,
                        'urutan'       => $detail->urutan,
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
