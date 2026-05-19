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
    public function getSummary(Request $request)
    {
        $periodId = $request->query('period_id');

        $total = DB::table('alumni')->count();

        $sudahQuery = DB::table('answers')->distinct('alumni_id');
        if ($periodId) {
            $sudahQuery->where('survey_period_id', $periodId);
        }
        $sudah = $sudahQuery->count('alumni_id');

        return response()->json([
            'total_alumni' => $total,
            'sudah_isi'    => $sudah,
            'belum_isi'    => $total - $sudah,
        ]);
    }

    // ==================================================
    // Grafik Sebaran Jenis Instansi → f1101
    // ==================================================
    public function getInstansiChartData(Request $request)
    {
        $periodId = $request->query('period_id');
        $periodCond = $periodId ? "AND a.survey_period_id = ?" : '';

        // Urutan ?: period_id dulu (di JOIN), lalu kode_soal (di WHERE)
        $params = [];
        if ($periodId) $params[] = $periodId;
        $params[] = 'f1101';

        $data = DB::select("
            SELECT
                ad.value AS jenis_instansi,
                COUNT(ad.id) AS total
            FROM answer_details ad
            JOIN answers a   ON a.id = ad.answer_id $periodCond
            JOIN questions q ON q.id = a.question_id
            WHERE q.kode_soal = ?
              AND ad.value IS NOT NULL
              AND ad.value != ''
            GROUP BY ad.value
            ORDER BY total DESC
        ", $params);

        if (empty($data)) {
            return response()->json([['jenis_instansi' => 'Belum Ada Data', 'total' => 0]]);
        }

        return response()->json($data);
    }

    // ==================================================
    // Grafik Sebaran Profesi Lulusan → f8
    // ==================================================
    public function getProfesiChart(Request $request)
    {
        $periodId = $request->query('period_id');
        $periodCond = $periodId ? "AND a.survey_period_id = ?" : '';

        // Urutan ?: period_id dulu (di JOIN), lalu kode_soal (di WHERE)
        $params = [];
        if ($periodId) $params[] = $periodId;
        $params[] = 'f8';

        $data = DB::select("
            SELECT
                ad.value AS profesi,
                COUNT(DISTINCT a.alumni_id) AS total
            FROM answer_details ad
            JOIN answers a   ON a.id = ad.answer_id $periodCond
            JOIN questions q ON q.id = a.question_id
            WHERE q.kode_soal = ?
              AND ad.value IS NOT NULL
              AND ad.value != ''
            GROUP BY ad.value
            ORDER BY total DESC
        ", $params);

        if (empty($data)) {
            return response()->json([['profesi' => 'Belum Ada Data', 'total' => 0]]);
        }

        return response()->json($data);
    }

    // ==================================================
    // Tabel Rekap Alumni per Tahun Lulus
    //
    //   infokom       → f14: value IN ('Sangat Erat', 'Erat')
    //   noninfokom    → f14: value IN ('Cukup Erat', 'Kurang Erat', 'Tidak Sama Sekali')
    //   multinasional → f5d: value LIKE '%Multinasional%' OR LIKE '%Internasional%'
    //   nasional      → f5d: value LIKE '%Nasional%' (exclude multinasional)
    //   wirausaha     → f8:  value LIKE '%Wiraswasta%' OR f5d LIKE '%wiraswasta%'
    // ==================================================
    public function getRekapAlumni(Request $request)
    {
        $periodId   = $request->query('period_id');
        $periodCond = $periodId ? "AND ans_any.survey_period_id = ?" : '';
        $periodF14  = $periodId ? "AND a_f14.survey_period_id = ?" : '';
        $periodF5d  = $periodId ? "AND a_f5d.survey_period_id = ?" : '';
        $periodF8   = $periodId ? "AND a_f8.survey_period_id = ?" : '';

        $params = [];
        if ($periodId) { $params[] = $periodId; $params[] = $periodId; $params[] = $periodId; $params[] = $periodId; }

        $data = DB::select("
            SELECT
                al.tahun_lulus AS tahunlulus,
                COUNT(DISTINCT al.id) AS jumlahlulusan,
                COUNT(DISTINCT CASE WHEN ans_any.alumni_id IS NOT NULL THEN al.id END) AS terlacaklulusan,
                COUNT(DISTINCT CASE WHEN q_f14.kode_soal = 'f14'
                    AND ad_f14.value IN ('Sangat Erat', 'Erat') THEN al.id END) AS infokom,
                COUNT(DISTINCT CASE WHEN q_f14.kode_soal = 'f14'
                    AND ad_f14.value IN ('Cukup Erat', 'Kurang Erat', 'Tidak Sama Sekali') THEN al.id END) AS noninfokom,
                COUNT(DISTINCT CASE WHEN q_f5d.kode_soal = 'f5d'
                    AND (ad_f5d.value LIKE '%Multinasional%' OR ad_f5d.value LIKE '%Internasional%') THEN al.id END) AS multinasional,
                COUNT(DISTINCT CASE WHEN q_f5d.kode_soal = 'f5d'
                    AND ad_f5d.value LIKE '%Nasional%'
                    AND ad_f5d.value NOT LIKE '%Multinasional%'
                    AND ad_f5d.value NOT LIKE '%Internasional%' THEN al.id END) AS nasional,
                COUNT(DISTINCT CASE WHEN q_f8.kode_soal = 'f8'
                    AND ad_f8.value LIKE '%Wiraswasta%' THEN al.id END) AS wirausaha
            FROM alumni al
            LEFT JOIN (SELECT DISTINCT alumni_id, survey_period_id FROM answers) ans_any
                ON ans_any.alumni_id = al.id $periodCond
            LEFT JOIN answers a_f14 ON a_f14.alumni_id = al.id $periodF14
            LEFT JOIN questions q_f14 ON q_f14.id = a_f14.question_id AND q_f14.kode_soal = 'f14'
            LEFT JOIN answer_details ad_f14 ON ad_f14.answer_id = a_f14.id
            LEFT JOIN answers a_f5d ON a_f5d.alumni_id = al.id $periodF5d
            LEFT JOIN questions q_f5d ON q_f5d.id = a_f5d.question_id AND q_f5d.kode_soal = 'f5d'
            LEFT JOIN answer_details ad_f5d ON ad_f5d.answer_id = a_f5d.id
            LEFT JOIN answers a_f8 ON a_f8.alumni_id = al.id $periodF8
            LEFT JOIN questions q_f8 ON q_f8.id = a_f8.question_id AND q_f8.kode_soal = 'f8'
            LEFT JOIN answer_details ad_f8 ON ad_f8.answer_id = a_f8.id
            GROUP BY al.tahun_lulus
            ORDER BY al.tahun_lulus
        ", $params);

        return response()->json($data);
    }

    // ==================================================
    // Tabel Rata-rata Masa Tunggu → f502
    // Menggunakan CASE WHEN agar COALESCE bekerja benar
    // ketika AVG menghasilkan NULL (tidak ada data)
    // ==================================================
    public function getAverageWaitingTime(Request $request)
    {
        $periodId   = $request->query('period_id');
        $periodCond = $periodId ? "AND ans.survey_period_id = $periodId" : '';

        $results = DB::select('
            SELECT
                COALESCE(al.tahun_lulus, 0) AS tahunlulus,
                COUNT(DISTINCT al.id) AS jumlahlulusan,
                COUNT(DISTINCT CASE WHEN ans.id IS NOT NULL THEN al.id END) AS terlacaklulusan,
                CASE
                    WHEN AVG(CASE WHEN q.kode_soal = \'f502\' AND ad.value REGEXP \'^[0-9]+$\' THEN CAST(ad.value AS UNSIGNED) END) IS NOT NULL
                    THEN CONCAT(ROUND(AVG(CASE WHEN q.kode_soal = \'f502\' AND ad.value REGEXP \'^[0-9]+$\' THEN CAST(ad.value AS UNSIGNED) END), 1), \' bulan\')
                    ELSE \'N/A\'
                END AS rata_rata_waktu_tunggu_bulan
            FROM alumni al
            LEFT JOIN answers ans       ON ans.alumni_id = al.id ' . $periodCond . '
            LEFT JOIN answer_details ad ON ad.answer_id  = ans.id
            LEFT JOIN questions q       ON q.id          = ans.question_id
            GROUP BY al.tahun_lulus
            ORDER BY tahunlulus
        ');

        return response()->json($results);
    }

    // ==================================================
    // Rata-rata Penghasilan Alumni → f505
    // Mengembalikan summary global + breakdown per tahun lulus
    // ==================================================
    public function getPenghasilan(Request $request)
    {
        $periodId   = $request->query('period_id');
        $periodCond = $periodId ? "AND a.survey_period_id = $periodId" : '';

        // Summary global
        $summary = DB::selectOne("
            SELECT
                AVG(CAST(ad.value AS UNSIGNED)) AS rata_rata,
                MAX(CAST(ad.value AS UNSIGNED)) AS tertinggi,
                MIN(CAST(ad.value AS UNSIGNED)) AS terendah,
                COUNT(ad.id)                    AS total_responden
            FROM answer_details ad
            JOIN answers a   ON a.id  = ad.answer_id $periodCond
            JOIN questions q ON q.id  = a.question_id
            WHERE q.kode_soal = 'f505'
              AND ad.value REGEXP '^[0-9]+\$'
              AND CAST(ad.value AS UNSIGNED) > 0
        ");

        // Breakdown per tahun lulus
        $perTahun = DB::select("
            SELECT
                al.tahun_lulus,
                COUNT(ad.id)                                AS total_responden,
                ROUND(AVG(CAST(ad.value AS UNSIGNED)), 0)   AS rata_rata,
                MAX(CAST(ad.value AS UNSIGNED))             AS tertinggi,
                MIN(CAST(ad.value AS UNSIGNED))             AS terendah
            FROM answer_details ad
            JOIN answers a   ON a.id  = ad.answer_id $periodCond
            JOIN questions q ON q.id  = a.question_id
            JOIN alumni al   ON al.id = a.alumni_id
            WHERE q.kode_soal = 'f505'
              AND ad.value REGEXP '^[0-9]+\$'
              AND CAST(ad.value AS UNSIGNED) > 0
            GROUP BY al.tahun_lulus
            ORDER BY al.tahun_lulus
        ");

        return response()->json([
            'summary'   => $summary,
            'per_tahun' => $perTahun,
        ]);
    }

    // ==================================================
    // Tabel Penilaian Kepuasan Pengguna Lulusan
    // Kode soal kompetensi: f1761–f1774
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
                SUM(CASE WHEN ad.value = 'Sangat Tinggi' THEN 1 ELSE 0 END) AS sangat_baik,
                SUM(CASE WHEN ad.value = 'Tinggi'        THEN 1 ELSE 0 END) AS baik,
                SUM(CASE WHEN ad.value = 'Sedang'        THEN 1 ELSE 0 END) AS cukup,
                SUM(CASE WHEN ad.value IN ('Rendah', 'Sangat Rendah') THEN 1 ELSE 0 END) AS kurang,
                COUNT(ad.id) AS total
            FROM answer_details ad
            JOIN answers a   ON a.id  = ad.answer_id
            JOIN questions q ON q.id  = a.question_id
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
    // Dipakai oleh semua grafik kompetensi
    // ==================================================
    private function getSkillChartData(string $kodeSoal): \Illuminate\Http\JsonResponse
    {
        $periodId   = request()->query('period_id');
        $periodCond = $periodId ? "AND a.survey_period_id = ?" : '';

        // Parameter order harus sesuai urutan ? di SQL:
        // 1. period_id (di JOIN/WHERE atas), 2. kode_soal (di WHERE bawah)
        $params = [];
        if ($periodId) $params[] = $periodId;
        $params[] = $kodeSoal;

        $results = DB::select("
            SELECT
                CASE ad.value
                    WHEN 'Sangat Tinggi' THEN 'Sangat Baik'
                    WHEN 'Tinggi'        THEN 'Baik'
                    WHEN 'Sedang'        THEN 'Cukup'
                    WHEN 'Rendah'        THEN 'Kurang'
                    WHEN 'Sangat Rendah' THEN 'Kurang'
                    ELSE ad.value
                END AS tingkat_kepuasan,
                COUNT(ad.id) AS jumlah_responden_per_tingkat
            FROM answer_details ad
            JOIN answers a   ON a.id = ad.answer_id $periodCond
            JOIN questions q ON q.id = a.question_id
            WHERE q.kode_soal = ?
              AND ad.value IS NOT NULL
            GROUP BY tingkat_kepuasan
            ORDER BY jumlah_responden_per_tingkat DESC
        ", $params);

        return response()->json($results);
    }

    // ==================================================
    // Grafik Kompetensi
    // ==================================================

    /** Kerjasama Tim → f1771 */
    public function getKerjaSama()
    {
        return $this->getSkillChartData('f1771');
    }

    /** Keahlian Bidang Ilmu → f1763 */
    public function keahlianChart()
    {
        return $this->getSkillChartData('f1763');
    }

    /** Kemampuan Bahasa Inggris → f1765 */
    public function kemampuanBahasaChart()
    {
        return $this->getSkillChartData('f1765');
    }

    /** Kemampuan Komunikasi → f1769 */
    public function kemampuanKomunikasiChart()
    {
        return $this->getSkillChartData('f1769');
    }

    /** Pengembangan Diri → f1773 */
    public function pengembanganDiriChart()
    {
        return $this->getSkillChartData('f1773');
    }
}
