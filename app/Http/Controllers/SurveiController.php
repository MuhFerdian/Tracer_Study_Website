<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PertanyaanModel;
use App\Models\JawabanSurveiModel;
use App\Models\alumniModel;

class SurveiController extends Controller
{
    /**
     * Mengambil semua pertanyaan survei (kecuali pertanyaan saran/masukan)
     */
    public function getPertanyaan()
    {
        $pertanyaan = PertanyaanModel::select('pertanyaan_id', 'pertanyaan')
            ->whereNotIn('pertanyaan_id', [8, 9])
            ->get();

        return response()->json($pertanyaan);
    }

    /**
     * Mengambil semua pertanyaan (untuk API Flutter)
     */
    public function surveiPertanyaan()
    {
        return response()->json(PertanyaanModel::all());
    }

    /**
     * Menyimpan jawaban survei
     */
    public function store(Request $request)
    {
        $request->validate([
            'alumni_id'            => 'required|exists:alumni,alumni_id',
            'jawaban'              => 'required|array',
            'jawaban.*'            => 'required|in:Kurang,Cukup,Baik,Sangat Baik',
            'kompetensi_tambahan'  => 'nullable|string',
            'saran_kurikulum'      => 'nullable|string',
        ]);

        try {
            // Simpan setiap jawaban penilaian kompetensi
            foreach ($request->jawaban as $pertanyaan_id => $nilai) {
                JawabanSurveiModel::create([
                    'pertanyaan_id' => $pertanyaan_id,
                    'alumni_id'     => $request->alumni_id,
                    'jawaban'       => $nilai,
                ]);
            }

            // Simpan kompetensi_tambahan sebagai jawaban pertanyaan_id 8
            if ($request->filled('kompetensi_tambahan')) {
                JawabanSurveiModel::create([
                    'pertanyaan_id' => 8,
                    'alumni_id'     => $request->alumni_id,
                    'jawaban'       => $request->kompetensi_tambahan,
                ]);
            }

            // Simpan saran_kurikulum sebagai jawaban pertanyaan_id 9
            if ($request->filled('saran_kurikulum')) {
                JawabanSurveiModel::create([
                    'pertanyaan_id' => 9,
                    'alumni_id'     => $request->alumni_id,
                    'jawaban'       => $request->saran_kurikulum,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Survei berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan survei: ' . $e->getMessage(),
            ], 500);
        }
    }
}
