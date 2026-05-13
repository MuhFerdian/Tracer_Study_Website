<?php

namespace App\Http\Controllers;

use App\Models\alumniModel as Alumni;
use Illuminate\Support\Facades\DB;

class TracerController extends Controller
{
    public function statistikAlumni()
    {
        $total = Alumni::count();

        // Ambil dari jawaban f8 (status saat ini)
        // Opsi: 1=Bekerja, 2=Belum memungkinkan, 3=Wiraswasta, 4=Melanjutkan Pendidikan, 5=Mencari kerja
        $kerja = DB::table('answers as a')
            ->join('answer_details as ad', 'ad.answer_id', '=', 'a.id')
            ->join('questions as q', 'q.id', '=', 'a.question_id')
            ->join('question_options as qo', 'qo.id', '=', 'ad.option_id')
            ->where('q.kode_soal', 'f8')
            ->where('qo.value', '1') // Bekerja (full time / part time)
            ->distinct('a.alumni_id')
            ->count('a.alumni_id');

        $wirausaha = DB::table('answers as a')
            ->join('answer_details as ad', 'ad.answer_id', '=', 'a.id')
            ->join('questions as q', 'q.id', '=', 'a.question_id')
            ->join('question_options as qo', 'qo.id', '=', 'ad.option_id')
            ->where('q.kode_soal', 'f8')
            ->where('qo.value', '3') // Wiraswasta
            ->distinct('a.alumni_id')
            ->count('a.alumni_id');

        return response()->json([
            'status' => true,
            'data' => [
                'total'     => $total,
                'kerja'     => $kerja,
                'wirausaha' => $wirausaha,
            ]
        ]);
    }
}