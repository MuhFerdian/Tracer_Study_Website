<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('layoutAdmin.index');
    }
    // public function indexDosen()
    // {
    //     return view('layoutAdmin.index'); // atau beda view
    // }

    public function getSummary()
    {
        $total = DB::table('alumni')->count();

        $sudah = DB::table('answers')
            ->distinct('alumni_id')
            ->count('alumni_id');

        return response()->json([
            'total_alumni' => $total,
            'sudah_isi' => $sudah,
            'belum_isi' => $total - $sudah,
        ]);
    }
    public function getInstansiChartData()
    {
        $data = DB::table('alumni')
            ->selectRaw("
                CASE 
                    WHEN nama_instansi IS NULL OR nama_instansi = '' THEN 'Belum Diisi'
                    ELSE nama_instansi
                END as jenis_instansi,
                COUNT(*) as total
            ")
            ->groupBy('jenis_instansi')
            ->orderByDesc('total')
            ->get();

        return response()->json($data);
    }
    public function getProfesiChart()
    {
        $data = DB::table('alumni')
            ->selectRaw("
                CASE 
                    WHEN posisi IS NOT NULL AND posisi != '' THEN posisi
                    WHEN status_pekerjaan IS NOT NULL AND status_pekerjaan != '' THEN status_pekerjaan
                    ELSE 'Belum Diisi'
                END as profesi,
                COUNT(*) as total
            ")
            ->groupBy('profesi')
            ->orderByDesc('total')
            ->get();

        return response()->json($data);
    }

    public function getRekapAlumni()
    {
        $data = DB::table('alumni')
            ->select(
                'tahun_lulus as tahunlulus',
                DB::raw('COUNT(*) as jumlahlulusan'),
                DB::raw('COUNT(CASE WHEN nama_instansi IS NOT NULL OR posisi IS NOT NULL THEN 1 END) as terlacaklulusan'),
                DB::raw("SUM(CASE WHEN posisi LIKE '%IT%' OR posisi LIKE '%Developer%' THEN 1 ELSE 0 END) as infokom"),
                DB::raw("SUM(CASE WHEN posisi NOT LIKE '%IT%' AND posisi IS NOT NULL THEN 1 ELSE 0 END) as noninfokom"),
                DB::raw("SUM(CASE WHEN nama_instansi LIKE '%PT%' THEN 1 ELSE 0 END) as nasional"),
                DB::raw("SUM(CASE WHEN posisi LIKE '%wirausaha%' THEN 1 ELSE 0 END) as wirausaha"),
                DB::raw("0 as multinasional")
            )
            ->groupBy('tahun_lulus')
            ->orderBy('tahun_lulus')
            ->get();

        return response()->json($data);
    }

    public function getAverageWaitingTime()
    {
        $results = DB::select("
            SELECT
                COALESCE(a.tahun_lulus, 0) AS tahunlulus,
                COUNT(a.id) AS jumlahlulusan,
                SUM(CASE WHEN a.status_pekerjaan IS NOT NULL OR a.nama_instansi IS NOT NULL OR a.posisi IS NOT NULL THEN 1 ELSE 0 END) AS terlacaklulusan,
                'N/A' AS rata_rata_waktu_tunggu_bulan
            FROM
                alumni AS a
            GROUP BY
                a.tahun_lulus
            ORDER BY
                tahunlulus;
        ");

        return response()->json($results);
    }
    public function getAlumniSatisfaction()
    {
        $results = DB::select("
            SELECT
                q.pertanyaan AS jenis_kemampuan,
                SUM(CASE WHEN qo.option_text = 'Sangat Baik' THEN 1 ELSE 0 END) AS sangat_baik,
                SUM(CASE WHEN qo.option_text = 'Baik' THEN 1 ELSE 0 END) AS baik,
                SUM(CASE WHEN qo.option_text = 'Cukup' THEN 1 ELSE 0 END) AS cukup,
                SUM(CASE WHEN qo.option_text = 'Kurang' THEN 1 ELSE 0 END) AS kurang,
                COUNT(ad.id) as total
            FROM answer_details ad
            JOIN answers a ON a.id = ad.answer_id
            JOIN questions q ON q.id = a.question_id
            JOIN question_options qo ON qo.id = ad.question_option_id
            GROUP BY q.id, q.pertanyaan
            ORDER BY q.id
        ");

        // Calculate percentages and format them
        $formattedResults = [];
        foreach ($results as $item) {
            $total = (int)$item->total; // Ensure total is an integer for division

            $sangat_baik_persen = ($total > 0) ? ($item->sangat_baik / $total) * 100 : 0;
            $baik_persen = ($total > 0) ? ($item->baik / $total) * 100 : 0;
            $cukup_persen = ($total > 0) ? ($item->cukup / $total) * 100 : 0;
            $kurang_persen = ($total > 0) ? ($item->kurang / $total) * 100 : 0;

            $formattedResults[] = (object) [
                'jenis_kemampuan' => $item->jenis_kemampuan,
                'sangat_baik_persen' => number_format($sangat_baik_persen, 2, '.', '') . '%',
                'baik_persen' => number_format($baik_persen, 2, '.', '') . '%',
                'cukup_persen' => number_format($cukup_persen, 2, '.', '') . '%',
                'kurang_persen' => number_format($kurang_persen, 2, '.', '') . '%',
                'sangat_baik_raw' => $sangat_baik_persen, // Keep raw for total calculation
                'baik_raw' => $baik_persen,
                'cukup_raw' => $cukup_persen,
                'kurang_raw' => $kurang_persen,
            ];
        }

        return response()->json($formattedResults);
    }

    public function getKerjaSama()
    {
        $results = DB::select("
        SELECT
            p.pertanyaan AS jenis_kemampuan,
            j.jawaban AS tingkat_kepuasan,
            COUNT(j.jawaban_id) AS jumlah_responden_per_tingkat
        FROM
            jawaban AS j
        JOIN
            pertanyaan AS p ON j.pertanyaan_id = p.pertanyaan_id
        WHERE
            p.pertanyaan_id = 1 
        AND j.jawaban IN ('Sangat Baik', 'Baik', 'Cukup', 'Kurang') -- Ensure only valid satisfaction levels are counted
        GROUP BY
            p.pertanyaan,
            j.jawaban
        ORDER BY
            p.pertanyaan,
            CASE j.jawaban
                WHEN 'Sangat Baik' THEN 1
                WHEN 'Baik' THEN 2
                WHEN 'Cukup' THEN 3
                WHEN 'Kurang' THEN 4
                ELSE 5
            END;");
        return response()->json($results);
    }

    public function keahlianChart()
    {
        $data = DB::table('jawaban as j')
            ->join('pertanyaan as p', 'j.pertanyaan_id', '=', 'p.pertanyaan_id')
            ->select('p.pertanyaan as jenis_kemampuan', 'j.jawaban as tingkat_kepuasan', DB::raw('COUNT(j.jawaban_id) as jumlah_responden_per_tingkat'))
            ->where('p.pertanyaan_id', 2)
            ->whereIn('j.jawaban', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang'])
            ->groupBy('p.pertanyaan', 'j.jawaban')
            ->orderByRaw("
            CASE j.jawaban
                WHEN 'Sangat Baik' THEN 1
                WHEN 'Baik' THEN 2
                WHEN 'Cukup' THEN 3
                WHEN 'Kurang' THEN 4
                ELSE 5
            END
        ")
            ->get();

        return response()->json($data);
    }
    public function kemampuanBahasaChart()
    {
        $data = DB::table('jawaban as j')
            ->join('pertanyaan as p', 'j.pertanyaan_id', '=', 'p.pertanyaan_id')
            ->select('p.pertanyaan as jenis_kemampuan', 'j.jawaban as tingkat_kepuasan', DB::raw('COUNT(j.jawaban_id) as jumlah_responden_per_tingkat'))
            ->where('p.pertanyaan_id', 3)
            ->whereIn('j.jawaban', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang'])
            ->groupBy('p.pertanyaan', 'j.jawaban')
            ->orderByRaw("
            CASE j.jawaban
                WHEN 'Sangat Baik' THEN 1
                WHEN 'Baik' THEN 2
                WHEN 'Cukup' THEN 3
                WHEN 'Kurang' THEN 4
                ELSE 5
            END
        ")
            ->get();

        return response()->json($data);
    }
    public function kemampuanKomunikasiChart()
    {
        $data = DB::table('jawaban as j')
            ->join('pertanyaan as p', 'j.pertanyaan_id', '=', 'p.pertanyaan_id')
            ->select('p.pertanyaan as jenis_kemampuan', 'j.jawaban as tingkat_kepuasan', DB::raw('COUNT(j.jawaban_id) as jumlah_responden_per_tingkat'))
            ->where('p.pertanyaan_id', 4)
            ->whereIn('j.jawaban', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang'])
            ->groupBy('p.pertanyaan', 'j.jawaban')
            ->orderByRaw("
            CASE j.jawaban
                WHEN 'Sangat Baik' THEN 1
                WHEN 'Baik' THEN 2
                WHEN 'Cukup' THEN 3
                WHEN 'Kurang' THEN 4
                ELSE 5
            END
        ")
            ->get();

        return response()->json($data);
    }
    public function pengembanganDiriChart()
    {
        $data = DB::table('jawaban as j')
            ->join('pertanyaan as p', 'j.pertanyaan_id', '=', 'p.pertanyaan_id')
            ->select('p.pertanyaan as jenis_kemampuan', 'j.jawaban as tingkat_kepuasan', DB::raw('COUNT(j.jawaban_id) as jumlah_responden_per_tingkat'))
            ->where('p.pertanyaan_id', 5)
            ->whereIn('j.jawaban', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang'])
            ->groupBy('p.pertanyaan', 'j.jawaban')
            ->orderByRaw("
            CASE j.jawaban
                WHEN 'Sangat Baik' THEN 1
                WHEN 'Baik' THEN 2
                WHEN 'Cukup' THEN 3
                WHEN 'Kurang' THEN 4
                ELSE 5
            END
        ")
            ->get();

        return response()->json($data);
    }
    public function kepemimpinanChart()
    {
        $data = DB::table('jawaban as j')
            ->join('pertanyaan as p', 'j.pertanyaan_id', '=', 'p.pertanyaan_id')
            ->select('p.pertanyaan as jenis_kemampuan', 'j.jawaban as tingkat_kepuasan', DB::raw('COUNT(j.jawaban_id) as jumlah_responden_per_tingkat'))
            ->where('p.pertanyaan_id', 6)
            ->whereIn('j.jawaban', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang'])
            ->groupBy('p.pertanyaan', 'j.jawaban')
            ->orderByRaw("
            CASE j.jawaban
                WHEN 'Sangat Baik' THEN 1
                WHEN 'Baik' THEN 2
                WHEN 'Cukup' THEN 3
                WHEN 'Kurang' THEN 4
                ELSE 5
            END
        ")
            ->get();

        return response()->json($data);
    }
    public function etosKerjaChart()
    {
        $data = DB::table('jawaban as j')
            ->join('pertanyaan as p', 'j.pertanyaan_id', '=', 'p.pertanyaan_id')
            ->select(
                'p.pertanyaan as jenis_kemampuan',
                'j.jawaban as tingkat_kepuasan',
                DB::raw('COUNT(j.jawaban_id) as jumlah_responden_per_tingkat')
            )
            ->where('p.pertanyaan_id', 7)
            ->whereIn('j.jawaban', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang'])
            ->groupBy('p.pertanyaan', 'j.jawaban')
            ->orderByRaw("
            CASE j.jawaban
                WHEN 'Sangat Baik' THEN 1
                WHEN 'Baik' THEN 2
                WHEN 'Cukup' THEN 3
                WHEN 'Kurang' THEN 4
                ELSE 5
            END
        ")
            ->get();

        return response()->json($data);
    }
}
