<?php

namespace App\Http\Controllers;

use App\Models\alumniModel;
use App\Models\Question;
use App\Models\SurveyPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExportController extends Controller
{
    // =========================================
    // LAPORAN PDF – Ringkasan Jawaban Alumni
    // =========================================
    public function laporanPdf(Request $request)
    {
        $periodId = $request->query('period_id');
        $periode  = $periodId ? SurveyPeriod::find($periodId) : null;

        if (!$periode) {
            $periode  = SurveyPeriod::where('status', 'aktif')->first();
            $periodId = $periode?->id;
        }

        $totalAlumni    = DB::table('alumni')->count();
        $respondenQuery = DB::table('answers')->distinct('alumni_id');
        if ($periodId) {
            $respondenQuery->where('survey_period_id', $periodId);
        }
        $totalResponden = $respondenQuery->count('alumni_id');

        $questions = Question::where('is_archived', false)
            ->orderBy('urutan')
            ->with([
                'options' => fn($q) => $q->orderBy('urutan'),
                'details' => fn($q) => $q->orderBy('urutan'),
            ])
            ->get()
            ->map(fn($q) => $this->enrichQuestion($q, $periodId));

        return view('layoutAdmin.rekap.laporan_pdf', compact(
            'questions',
            'periode',
            'totalAlumni',
            'totalResponden'
        ));
    }

    // =========================================
    // PRIVATE: Enrich pertanyaan dengan distribusi jawaban
    // =========================================
    private function enrichQuestion(Question $q, ?int $periodId): Question
    {
        $pid = (int) $periodId;
        $periodCond = $pid > 0 ? "AND a.survey_period_id = {$pid}" : '';

        // Total responden per pertanyaan
        $row = DB::selectOne(
            "SELECT COUNT(DISTINCT a.alumni_id) AS cnt
             FROM answers a
             WHERE a.question_id = ? {$periodCond}",
            [$q->id]
        );
        $q->totalResponden = (int) ($row->cnt ?? 0);

        if ($q->type === 'text') {
            $rows = DB::select(
                "SELECT ad.value
                 FROM answer_details ad
                 JOIN answers a ON a.id = ad.answer_id {$periodCond}
                 WHERE a.question_id = ?
                   AND ad.value IS NOT NULL
                   AND ad.value != ''
                 ORDER BY a.created_at DESC
                 LIMIT 50",
                [$q->id]
            );
            $q->textAnswers = collect($rows)->pluck('value');
        } elseif ($q->type === 'scale') {
            $rows = DB::select(
                "SELECT ad.value AS label, COUNT(*) AS count
                 FROM answer_details ad
                 JOIN answers a ON a.id = ad.answer_id {$periodCond}
                 WHERE a.question_id = ?
                   AND ad.value IS NOT NULL
                 GROUP BY ad.value
                 ORDER BY CAST(ad.value AS UNSIGNED)",
                [$q->id]
            );
            $q->distribution = collect($rows);

            $avgRow = DB::selectOne(
                "SELECT AVG(CAST(ad.value AS UNSIGNED)) AS avg_val
                 FROM answer_details ad
                 JOIN answers a ON a.id = ad.answer_id {$periodCond}
                 WHERE a.question_id = ?
                   AND ad.value REGEXP '^[0-9]+$'",
                [$q->id]
            );
            $q->avgValue = $avgRow->avg_val ?? null;
        } elseif ($q->type === 'matrix') {
            $q->matrixOptions = $q->options;
            $q->matrixRows    = $q->details;

            // Jawaban matrix disimpan sebagai JSON object {"item_label": "value"} di ad.value
            // Kita ambil semua jawaban lalu parse per item_label × option_label
            $answerRows = DB::select(
                "SELECT ad.value
                 FROM answer_details ad
                 JOIN answers a ON a.id = ad.answer_id
                 WHERE a.question_id = ?
                   AND ad.value IS NOT NULL
                   AND ad.value != ''
                   " . ($pid > 0 ? "AND a.survey_period_id = {$pid}" : ""),
                [$q->id]
            );

            // matrixData[item_label][option_label] = count
            $matrixData     = [];
            $matrixRowTotal = [];

            foreach ($answerRows as $ar) {
                $decoded = json_decode($ar->value, true);
                if (!is_array($decoded)) continue;

                // Handle nested JSON: jika value dari key pertama adalah JSON object lagi
                // Contoh data lama: {"anjay": "{\"gacor\":\"4\",\"anjay\":\"1\"}", "gacor": null}
                // Kita perlu unwrap ke format flat: {"anjay":"1","gacor":"4","bagus":"3"}
                $firstVal = reset($decoded);
                if (is_string($firstVal) && strlen($firstVal) > 0 && $firstVal[0] === '{') {
                    $innerDecoded = json_decode($firstVal, true);
                    if (is_array($innerDecoded)) {
                        // Gunakan inner JSON sebagai data sebenarnya
                        $decoded = $innerDecoded;
                    }
                }

                foreach ($decoded as $itemLabel => $optionValue) {
                    if ($optionValue === null || $optionValue === '') continue;
                    $itemLabel   = (string) $itemLabel;
                    $optionValue = (string) $optionValue;

                    if (!isset($matrixData[$itemLabel][$optionValue])) {
                        $matrixData[$itemLabel][$optionValue] = 0;
                    }
                    $matrixData[$itemLabel][$optionValue]++;
                    $matrixRowTotal[$itemLabel] = ($matrixRowTotal[$itemLabel] ?? 0) + 1;
                }
            }

            $q->matrixData     = $matrixData;
            $q->matrixRowTotal = $matrixRowTotal;
        } else {
            // single / multiple — distribusi berdasarkan value (label teks)
            // Jawaban bisa tersimpan sebagai plain string ATAU JSON array ["opsi"]
            // Kita hitung per option label dengan JSON_CONTAINS dan LIKE fallback

            $options = $q->options()->orderBy('urutan')->get();
            $distribution = $options->map(function ($opt) use ($q, $periodCond) {
                $pid = (int) ($periodCond ? preg_replace('/\D/', '', $periodCond) : 0);

                // Hitung: value = plain string match
                $plainCount = DB::table('answer_details as ad')
                    ->join('answers as a', 'a.id', '=', 'ad.answer_id')
                    ->where('a.question_id', $q->id)
                    ->whereRaw('TRIM(LOWER(ad.value)) = TRIM(LOWER(?))', [$opt->label])
                    ->when($pid > 0, fn($q) => $q->where('a.survey_period_id', $pid))
                    ->count();

                // Hitung: value = JSON array yang mengandung label ini
                $jsonCount = DB::table('answer_details as ad')
                    ->join('answers as a', 'a.id', '=', 'ad.answer_id')
                    ->where('a.question_id', $q->id)
                    ->whereRaw(
                        "JSON_VALID(ad.value) = 1 AND JSON_CONTAINS(LOWER(ad.value), LOWER(JSON_QUOTE(?)))",
                        [$opt->label]
                    )
                    ->when($pid > 0, fn($q) => $q->where('a.survey_period_id', $pid))
                    ->count();

                return (object) [
                    'id'    => $opt->id,
                    'label' => $opt->label,
                    'count' => $plainCount + $jsonCount,
                ];
            });

            $q->distribution = $distribution;
        }

        return $q;
    }

    // =========================================
    // HALAMAN ALUMNI BELUM MENGISI
    // =========================================
    public function showAlumniBelumMengisi()
    {
        $alumni = alumniModel::whereNotIn(
            'id',
            DB::table('answers')->select('alumni_id')->distinct()
        )->get();

        return view('layoutAdmin.rekap.export_rekap_alumni_belum_mengisi', compact('alumni'));
    }

    // =========================================
    // HALAMAN ALUMNI SUDAH MENGISI
    // =========================================
    public function showAlumniSudahMengisi()
    {
        $alumni = alumniModel::whereIn(
            'id',
            DB::table('answers')->select('alumni_id')->distinct()
        )->get();

        return view('layoutAdmin.rekap.export_rekap_alumni', compact('alumni'));
    }

    // =========================================
    // EXPORT SEMUA DATA ALUMNI
    // =========================================
    public function exportExcel()
    {
        $alumni = alumniModel::select([
            'id',
            'nama',
            'nim',
            'prodi',
            'no_hp',
            'email',
            'alamat',
            'tempat_lahir',
            'tanggal_lahir',
            'angkatan',
            'tahun_lulus',
        ])->orderBy('id')->get();

        if ($alumni->isEmpty()) {
            return redirect()->back()->with('error', 'Data alumni tracer study masih kosong!');
        }

        return $this->generateExcel(
            $this->enrichWithAnswers($alumni),
            'DATA ALUMNI TRACER STUDY',
            'data_alumni'
        );
    }

    // =========================================
    // EXPORT ALUMNI SUDAH MENGISI
    // =========================================
    public function exportExcelSudahMengisi()
    {
        $alumni = alumniModel::whereIn('id', DB::table('answers')->select('alumni_id')->distinct())
            ->select([
                'id',
                'nama',
                'nim',
                'prodi',
                'no_hp',
                'email',
                'alamat',
                'tempat_lahir',
                'tanggal_lahir',
                'angkatan',
                'tahun_lulus',
            ])->orderBy('id')->get();

        if ($alumni->isEmpty()) {
            return redirect()->back()->with('error', 'Data alumni yang sudah mengisi tracer study masih kosong!');
        }

        return $this->generateExcel(
            $this->enrichWithAnswers($alumni),
            'DATA ALUMNI SUDAH MENGISI TRACER STUDY',
            'alumni_sudah_mengisi'
        );
    }

    // =========================================
    // EXPORT ALUMNI BELUM MENGISI
    // =========================================
    public function exportExcelBelumMengisi()
    {
        $data = alumniModel::whereNotIn(
            'id',
            DB::table('answers')->select('alumni_id')->distinct()
        )->select([
            'nama',
            'nim',
            'prodi',
            'no_hp',
            'email',
            'alamat',
            'tempat_lahir',
            'tanggal_lahir',
            'angkatan',
            'tahun_lulus',
        ])->orderBy('id')->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Data alumni yang belum mengisi tracer study masih kosong!');
        }

        return $this->generateExcelBelumMengisi(
            $data,
            'DATA ALUMNI BELUM MENGISI TRACER STUDY',
            'alumni_belum_mengisi'
        );
    }

    // =========================================
    // PRIVATE: Enrich alumni dengan jawaban
    // =========================================
    private function enrichWithAnswers($alumni)
    {
        $alumniIds = $alumni->pluck('id')->toArray();

        $statusMap = DB::table('answers as a')
            ->join('answer_details as ad', 'ad.answer_id', '=', 'a.id')
            ->join('questions as q', 'q.id', '=', 'a.question_id')
            ->join('question_options as qo', 'qo.id', '=', 'ad.option_id')
            ->where('q.kode_soal', 'f8')
            ->whereIn('a.alumni_id', $alumniIds)
            ->select('a.alumni_id', 'qo.label as status_pekerjaan')
            ->get()->keyBy('alumni_id');

        $instansiMap = DB::table('answers as a')
            ->join('answer_details as ad', 'ad.answer_id', '=', 'a.id')
            ->join('questions as q', 'q.id', '=', 'a.question_id')
            ->where('q.kode_soal', 'f5b')
            ->whereIn('a.alumni_id', $alumniIds)
            ->select('a.alumni_id', 'ad.value as nama_instansi')
            ->get()->keyBy('alumni_id');

        $posisiMap = DB::table('answers as a')
            ->join('answer_details as ad', 'ad.answer_id', '=', 'a.id')
            ->join('questions as q', 'q.id', '=', 'a.question_id')
            ->join('question_options as qo', 'qo.id', '=', 'ad.option_id')
            ->where('q.kode_soal', 'f5c')
            ->whereIn('a.alumni_id', $alumniIds)
            ->select('a.alumni_id', 'qo.label as posisi')
            ->get()->keyBy('alumni_id');

        return $alumni->map(function ($item) use ($statusMap, $instansiMap, $posisiMap) {
            $item->status_pekerjaan = $statusMap[$item->id]->status_pekerjaan ?? '-';
            $item->nama_instansi    = $instansiMap[$item->id]->nama_instansi ?? '-';
            $item->posisi           = $posisiMap[$item->id]->posisi ?? '-';
            return $item;
        });
    }

    // =========================================
    // PRIVATE: Generate Excel (sudah mengisi / semua)
    // =========================================
    private function generateExcel($data, $title, $fileName)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:M1');
        $sheet->setCellValue('A1', $title);
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $headings = [
            'Nama',
            'NIM',
            'Program Studi',
            'No HP',
            'Email',
            'Alamat',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Angkatan',
            'Tahun Lulus',
            'Status Pekerjaan',
            'Nama Instansi',
            'Posisi',
        ];

        foreach ($headings as $col => $heading) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col + 1) . '3', $heading);
        }

        $lastCol = Coordinate::stringFromColumnIndex(count($headings));
        $sheet->getStyle("A3:{$lastCol}3")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E78']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        foreach (range(1, count($headings)) as $i) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }

        $row = 4;
        foreach ($data as $item) {
            $sheet->setCellValue("A{$row}", $item->nama);
            $sheet->setCellValue("B{$row}", $item->nim);
            $sheet->setCellValue("C{$row}", $item->prodi);
            $sheet->setCellValue("D{$row}", $item->no_hp);
            $sheet->setCellValue("E{$row}", $item->email);
            $sheet->setCellValue("F{$row}", $item->alamat);
            $sheet->setCellValue("G{$row}", $item->tempat_lahir ?? '-');
            $sheet->setCellValue("H{$row}", $item->tanggal_lahir
                ? \Carbon\Carbon::parse($item->tanggal_lahir)->format('d-m-Y') : '-');
            $sheet->setCellValue("I{$row}", $item->angkatan);
            $sheet->setCellValue("J{$row}", $item->tahun_lulus);
            $sheet->setCellValue("K{$row}", $item->status_pekerjaan);
            $sheet->setCellValue("L{$row}", $item->nama_instansi);
            $sheet->setCellValue("M{$row}", $item->posisi);
            $row++;
        }

        $sheet->getStyle("A4:{$lastCol}" . ($row - 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        return $this->downloadExcel($spreadsheet, $fileName);
    }

    // =========================================
    // PRIVATE: Generate Excel belum mengisi
    // =========================================
    private function generateExcelBelumMengisi($data, $title, $fileName)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', $title);
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $headings = [
            'Nama',
            'NIM',
            'Program Studi',
            'No HP',
            'Email',
            'Alamat',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Angkatan',
            'Tahun Lulus',
        ];

        foreach ($headings as $col => $heading) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col + 1) . '3', $heading);
        }

        $lastCol = Coordinate::stringFromColumnIndex(count($headings));
        $sheet->getStyle("A3:{$lastCol}3")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C00000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        foreach (range(1, count($headings)) as $i) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }

        $row = 4;
        foreach ($data as $item) {
            $sheet->setCellValue("A{$row}", $item->nama);
            $sheet->setCellValue("B{$row}", $item->nim);
            $sheet->setCellValue("C{$row}", $item->prodi);
            $sheet->setCellValue("D{$row}", $item->no_hp);
            $sheet->setCellValue("E{$row}", $item->email);
            $sheet->setCellValue("F{$row}", $item->alamat);
            $sheet->setCellValue("G{$row}", $item->tempat_lahir ?? '-');
            $sheet->setCellValue("H{$row}", $item->tanggal_lahir
                ? \Carbon\Carbon::parse($item->tanggal_lahir)->format('d-m-Y') : '-');
            $sheet->setCellValue("I{$row}", $item->angkatan);
            $sheet->setCellValue("J{$row}", $item->tahun_lulus);
            $row++;
        }

        $sheet->getStyle("A4:{$lastCol}" . ($row - 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        return $this->downloadExcel($spreadsheet, $fileName);
    }

    // =========================================
    // PRIVATE: Download Excel
    // =========================================
    private function downloadExcel(Spreadsheet $spreadsheet, string $fileName)
    {
        $writer   = new Xlsx($spreadsheet);
        $filename = $fileName . '_' . date('d-m-Y_H-i') . '.xlsx';

        return response()->streamDownload(
            fn() => $writer->save('php://output'),
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }
}
