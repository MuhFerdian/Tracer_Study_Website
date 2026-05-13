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
            JOIN answers a           ON a.id  = ad.answer_id
            JOIN questions q         ON q.id  = a.question_id
            JOIN question_options qo ON qo.id = ad.option_id
            WHERE q.kode_soal = 'f1101'
            GROUP BY qo.id, qo.label
            ORDER BY total DESC
        ");

        if (empty($data)) {
            return response()->json([
                ['jenis_instansi' => 'Belum Ada Data', 'total' => 0]
            ]);
        }

        return response()->json($data);
    }

    // ==================================================
    // Grafik Sebaran Profesi Lulusan
    // Diambil dari jawaban f8 (status saat ini)
    // ==================================================
    public function getProfesiChart()
    {
        $data = DB::select("
            SELECT
                qo.label AS profesi,
                COUNT(DISTINCT a.alumni_id) AS total
            FROM answers a
            JOIN answer_details ad   ON ad.answer_id  = a.id
            JOIN questions q         ON q.id           = a.question_id
            JOIN question_options qo ON qo.id          = ad.option_id
            WHERE q.kode_soal = 'f8'
            GROUP BY qo.id, qo.label
            ORDER BY total DESC
        ");

        if (empty($data)) {
            return response()->json([
                ['profesi' => 'Belum Ada Data', 'total' => 0]
            ]);
        }

        return response()->json($data);
    }

    // ==================================================
    // Tabel Rekap Alumni per Tahun Lulus
    // terlacaklulusan = alumni yang sudah ada jawaban
    // ==================================================
    public function getRekapAlumni()
    {
        $data = DB::select("
            SELECT
                al.tahun_lulus AS tahunlulus,
                COUNT(DISTINCT al.id) AS jumlahlulusan,
                COUNT(DISTINCT CASE WHEN ans.id IS NOT NULL THEN al.id END) AS terlacaklulusan,
                0 AS infokom,
                0 AS noninfokom,
                0 AS nasional,
                0 AS wirausaha,
                0 AS multinasional
            FROM alumni al
            LEFT JOIN answers ans ON ans.alumni_id = al.id
            GROUP BY al.tahun_lulus
            ORDER BY al.tahun_lulus
        ");

        return response()->json($data);
    }

    // ==================================================
    // Tabel Rata-rata Masa Tunggu
    // Diambil dari jawaban f502 (bulan dapat kerja pertama)
    // ==================================================
    public function getAverageWaitingTime()
    {
        $results = DB::select("
            SELECT
                COALESCE(al.tahun_lulus, 0) AS tahunlulus,
                COUNT(DISTINCT al.id) AS jumlahlulusan,
                COUNT(DISTINCT CASE WHEN ans.id IS NOT NULL THEN al.id END) AS terlacaklulusan,
                COALESCE(
                    CONCAT(
                        ROUND(AVG(CASE
                            WHEN q.kode_soal = 'f502' AND ad.value REGEXP '^[0-9]+\$'
                            THEN CAST(ad.value AS UNSIGNED)
                        END), 1),
                        ' bulan'
                    ),
                    'N/A'
                ) AS rata_rata_waktu_tunggu_bulan
            FROM alumni al
            LEFT JOIN answers ans       ON ans.alumni_id = al.id
            LEFT JOIN answer_details ad ON ad.answer_id  = ans.id
            LEFT JOIN questions q       ON q.id          = ans.question_id
            GROUP BY al.tahun_lulus
            ORDER BY tahunlulus
        ");

        return response()->json($results);
    }

    // ==================================================
    // Tabel Penilaian Kepuasan Pengguna Lulusan
    // Hanya untuk pertanyaan kompetensi (f1761-f1774)
    // Scale: Sangat Tinggi→Sangat Baik, Tinggi→Baik, Sedang→Cukup, Rendah/SangatRendah→Kurang
    // ==================================================
    public function getAlumniSatisfaction()
    {
        $kompetensiKodes = [
            'f1761','f1762','f1763','f1764','f1765','f1766',
            'f1767','f1768','f1769','f1770','f1771','f1772',
            'f1773','f1774',
        ];

        $placeholders = implode(',', array_fill(0, count($kompetensiKodes), '?'));

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
            WHERE q.kode_soal IN ($placeholders)
            GROUP BY q.id, q.pertanyaan
            ORDER BY q.urutan
        ", $kompetensiKodes);

        $formatted = [];
        foreach ($results as $item) {
            $total = (int) $item->total;
            $formatted[] = (object) [
                'jenis_kemampuan' => $item->jenis_kemampuan,
                'sangat_baik'     => $total > 0 ? number_format(($item->sangat_baik / $total) * 100, 1) . '%' : '0%',
                'baik'            => $total > 0 ? number_format(($item->baik        / $total) * 100, 1) . '%' : '0%',
                'cukup'           => $total > 0 ? number_format(($item->cukup       / $total) * 100, 1) . '%' : '0%',
                'kurang'          => $total > 0 ? number_format(($item->kurang      / $total) * 100, 1) . '%' : '0%',
            ];
        }

        return response()->json($formatted);
    }

    // ==================================================
    // PRIVATE HELPER: Distribusi Jawaban per Kode Soal
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
    // Chart Functions
    // ==================================================

    /** Grafik Kerjasama Tim → f1771 */
    public function getKerjaSama()
    {
        return $this->getSkillChartData('f1771');
    }

    /** Grafik Keahlian Bidang Ilmu → f1763 */
    public function keahlianChart()
    {
        return $this->getSkillChartData('f1763');
    }

    /** Grafik Kemampuan Bahasa Inggris → f1765 */
    public function kemampuanBahasaChart()
    {
        return $this->getSkillChartData('f1765');
    }

    /** Grafik Kemampuan Komunikasi → f1769 */
    public function kemampuanKomunikasiChart()
    {
        return $this->getSkillChartData('f1769');
    }

    /** Grafik Pengembangan Diri → f1773 */
    public function pengembanganDiriChart()
    {
        return $this->getSkillChartData('f1773');
    }

    /** Grafik Kepemimpinan — belum ada kode soal di seeder */
    public function kepemimpinanChart()
    {
        return response()->json([]);
    }

    /** Grafik Etos Kerja — belum ada kode soal di seeder */
    public function etosKerjaChart()
    {
        return response()->json([]);
    }
}
