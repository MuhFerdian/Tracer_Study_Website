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

    // ==================================================
    // Summary Cards (Total Alumni, Sudah Isi, Belum Isi)
    // ==================================================
    public function getSummary()
    {
        $total = DB::table('alumni')->count();
        $sudah = DB::table('answers')->distinct('alumni_id')->count('alumni_id');

        return response()->json([
            'total_alumni' => $total,
            'sudah_isi'    => $sudah,
            'belum_isi'    => $total - $sudah,
        ]);
    }

    // ==================================================
    // Grafik Sebaran Jenis Instansi
    // Diambil dari jawaban questionnaire f1101
    // ==================================================
    public function getInstansiChartData()
    {
        $data = DB::select("
            SELECT
                qo.label AS jenis_instansi,
                COUNT(ad.id) AS total
            FROM answer_details ad
            JOIN answers a    ON a.id  = ad.answer_id
            JOIN questions q  ON q.id  = a.question_id
            JOIN question_options qo ON qo.id = ad.option_id
            WHERE q.kode_soal = 'f1101'
            GROUP BY qo.id, qo.label
            ORDER BY total DESC
        ");

        // Fallback jika belum ada jawaban
        if (empty($data)) {
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
        }

        return response()->json($data);
    }

    // ==================================================
    // Grafik Sebaran Profesi Lulusan
    // ==================================================
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

    // ==================================================
    // Tabel Rekap Alumni per Tahun Lulus
    // ==================================================
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

    // ==================================================
    // Tabel Rata-rata Masa Tunggu
    // ==================================================
    public function getAverageWaitingTime()
    {
        $results = DB::select("
            SELECT
                COALESCE(a.tahun_lulus, 0) AS tahunlulus,
                COUNT(a.id) AS jumlahlulusan,
                SUM(CASE
                    WHEN a.status_pekerjaan IS NOT NULL
                      OR a.nama_instansi IS NOT NULL
                      OR a.posisi IS NOT NULL
                    THEN 1 ELSE 0
                END) AS terlacaklulusan,
                'N/A' AS rata_rata_waktu_tunggu_bulan
            FROM alumni AS a
            GROUP BY a.tahun_lulus
            ORDER BY tahunlulus
        ");

        return response()->json($results);
    }

    // ==================================================
    // Tabel Penilaian Kepuasan Pengguna Lulusan
    // Menggunakan schema baru: answer_details + question_options
    // Scale: Sangat Tinggi→Sangat Baik, Tinggi→Baik, Sedang→Cukup, Rendah/SangatRendah→Kurang
    // ==================================================
    public function getAlumniSatisfaction()
    {
        $results = DB::select("
            SELECT
                q.pertanyaan AS jenis_kemampuan,
                SUM(CASE WHEN qo.label = 'Sangat Tinggi' THEN 1 ELSE 0 END) AS sangat_baik,
                SUM(CASE WHEN qo.label = 'Tinggi'        THEN 1 ELSE 0 END) AS baik,
                SUM(CASE WHEN qo.label = 'Sedang'        THEN 1 ELSE 0 END) AS cukup,
                SUM(CASE WHEN qo.label IN ('Rendah', 'Sangat Rendah') THEN 1 ELSE 0 END) AS kurang,
                COUNT(ad.id) AS total
            FROM answer_details ad
            JOIN answers a           ON a.id  = ad.answer_id
            JOIN questions q         ON q.id  = a.question_id
            JOIN question_options qo ON qo.id = ad.option_id
            GROUP BY q.id, q.pertanyaan
            ORDER BY q.urutan
        ");

        $formatted = [];
        foreach ($results as $item) {
            $total = (int) $item->total;
            $formatted[] = (object) [
                'jenis_kemampuan' => $item->jenis_kemampuan,
                'sangat_baik'     => $total > 0 ? number_format(($item->sangat_baik / $total) * 100, 1) . '%' : '0%',
                'baik'            => $total > 0 ? number_format(($item->baik     / $total) * 100, 1) . '%' : '0%',
                'cukup'           => $total > 0 ? number_format(($item->cukup    / $total) * 100, 1) . '%' : '0%',
                'kurang'          => $total > 0 ? number_format(($item->kurang   / $total) * 100, 1) . '%' : '0%',
            ];
        }

        return response()->json($formatted);
    }

    // ==================================================
    // PRIVATE HELPER: Distribusi Jawaban per Kode Soal
    // Memetakan skala 5 (Sangat Rendah–Sangat Tinggi)
    // ke 4 kategori yang dipakai chart JS
    // ==================================================
    private function getSkillChartData(string $kodeSoal): \Illuminate\Http\JsonResponse
    {
        $results = DB::select("
            SELECT
                CASE qo.label
                    WHEN 'Sangat Tinggi' THEN 'Sangat Baik'
                    WHEN 'Tinggi'        THEN 'Baik'
                    WHEN 'Sedang'        THEN 'Cukup'
                    WHEN 'Rendah'        THEN 'Kurang'
                    WHEN 'Sangat Rendah' THEN 'Kurang'
                    ELSE qo.label
                END AS tingkat_kepuasan,
                COUNT(ad.id) AS jumlah_responden_per_tingkat
            FROM answer_details ad
            JOIN answers a           ON a.id  = ad.answer_id
            JOIN questions q         ON q.id  = a.question_id
            JOIN question_options qo ON qo.id = ad.option_id
            WHERE q.kode_soal = ?
            GROUP BY tingkat_kepuasan
            ORDER BY jumlah_responden_per_tingkat DESC
        ", [$kodeSoal]);

        return response()->json($results);
    }

    // ==================================================
    // Chart Functions — menggunakan kode_soal dari QuestionSeeder
    // ==================================================

    /** Grafik Kerjasama Tim → f1771 (saat lulus) */
    public function getKerjaSama()
    {
        return $this->getSkillChartData('f1771');
    }

    /** Grafik Keahlian Bidang Ilmu → f1763 (saat lulus) */
    public function keahlianChart()
    {
        return $this->getSkillChartData('f1763');
    }

    /** Grafik Kemampuan Bahasa Inggris → f1765 (saat lulus) */
    public function kemampuanBahasaChart()
    {
        return $this->getSkillChartData('f1765');
    }

    /** Grafik Kemampuan Komunikasi → f1769 (saat lulus) */
    public function kemampuanKomunikasiChart()
    {
        return $this->getSkillChartData('f1769');
    }

    /** Grafik Pengembangan Diri → f1773 (saat lulus) */
    public function pengembanganDiriChart()
    {
        return $this->getSkillChartData('f1773');
    }

    /**
     * Grafik Kepemimpinan — belum ada pertanyaan di seeder.
     * Tambahkan pertanyaan kepemimpinan via admin panel agar chart berisi data.
     */
    public function kepemimpinanChart()
    {
        return response()->json([]);
    }

    /**
     * Grafik Etos Kerja — belum ada pertanyaan di seeder.
     * Tambahkan pertanyaan etos kerja via admin panel agar chart berisi data.
     */
    public function etosKerjaChart()
    {
        return response()->json([]);
    }
}
