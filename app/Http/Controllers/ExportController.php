<?php

namespace App\Http\Controllers;

use App\Models\alumniModel;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExportController extends Controller
{
    /**
     * =========================================
     * HALAMAN ALUMNI BELUM MENGISI
     * =========================================
     */
    public function showAlumniBelumMengisi()
    {
        $alumni = alumniModel::whereNotIn(
            'id',
            DB::table('answers')
                ->select('alumni_id')
                ->distinct()
        )->get();

        return view(
            'layoutAdmin.rekap.export_rekap_alumni_belum_mengisi',
            compact('alumni')
        );
    }

    /**
     * =========================================
     * HALAMAN ALUMNI SUDAH MENGISI
     * =========================================
     */
    public function showAlumniSudahMengisi()
    {
        $alumni = alumniModel::whereIn(
            'id',
            DB::table('answers')
                ->select('alumni_id')
                ->distinct()
        )->get();

        return view(
            'layoutAdmin.rekap.export_rekap_alumni',
            compact('alumni')
        );
    }

    /**
     * =========================================
     * EXPORT SEMUA DATA ALUMNI
     * =========================================
     */
    public function exportExcel()
    {
        $alumni = alumniModel::select([
                'id', 'nama', 'nim', 'prodi', 'no_hp', 'email',
                'alamat', 'tempat_lahir', 'tanggal_lahir', 'angkatan', 'tahun_lulus'
            ])
            ->orderBy('id')
            ->get();

        if ($alumni->isEmpty()) {
            return redirect()->back()->with('error', 'Data alumni tracer study masih kosong!');
        }

        $data = $this->enrichWithAnswers($alumni);

        return $this->generateExcel($data, 'DATA ALUMNI TRACER STUDY', 'data_alumni');
    }

    /**
     * =========================================
     * EXPORT ALUMNI SUDAH MENGISI
     * =========================================
     */
    public function exportExcelSudahMengisi()
    {
        $alumni = alumniModel::whereIn('id', DB::table('answers')->select('alumni_id')->distinct())
            ->select([
                'id', 'nama', 'nim', 'prodi', 'no_hp', 'email',
                'alamat', 'tempat_lahir', 'tanggal_lahir', 'angkatan', 'tahun_lulus'
            ])
            ->orderBy('id')
            ->get();

        if ($alumni->isEmpty()) {
            return redirect()->back()->with('error', 'Data alumni yang sudah mengisi tracer study masih kosong!');
        }

        $data = $this->enrichWithAnswers($alumni);

        return $this->generateExcel($data, 'DATA ALUMNI SUDAH MENGISI TRACER STUDY', 'alumni_sudah_mengisi');
    }

    /**
     * =========================================
     * EXPORT ALUMNI BELUM MENGISI
     * =========================================
     */
    public function exportExcelBelumMengisi()
    {
        $data = alumniModel::whereNotIn(
                'id',
                DB::table('answers')
                    ->select('alumni_id')
                    ->distinct()
            )
            ->select([
                'nama', 'nim', 'prodi', 'no_hp', 'email',
                'alamat', 'tempat_lahir', 'tanggal_lahir', 'angkatan', 'tahun_lulus',
            ])
            ->orderBy('id')
            ->get();

        if ($data->isEmpty()) {
            return redirect()
                ->back()
                ->with('error', 'Data alumni yang belum mengisi tracer study masih kosong!');
        }

        return $this->generateExcelBelumMengisi(
            $data,
            'DATA ALUMNI BELUM MENGISI TRACER STUDY',
            'alumni_belum_mengisi'
        );
    }

    /**
     * =========================================
     * HELPER: Enrich alumni data dengan jawaban
     * Ambil status pekerjaan (f8) dan nama instansi (f5b) dari answers
     * =========================================
     */
    private function enrichWithAnswers($alumni)
    {
        $alumniIds = $alumni->pluck('id')->toArray();

        // Ambil jawaban f8 (status saat ini: Bekerja, Wiraswasta, dll)
        $statusMap = DB::table('answers as a')
            ->join('answer_details as ad', 'ad.answer_id', '=', 'a.id')
            ->join('questions as q', 'q.id', '=', 'a.question_id')
            ->join('question_options as qo', 'qo.id', '=', 'ad.option_id')
            ->where('q.kode_soal', 'f8')
            ->whereIn('a.alumni_id', $alumniIds)
            ->select('a.alumni_id', 'qo.label as status_pekerjaan')
            ->get()
            ->keyBy('alumni_id');

        // Ambil jawaban f5b (nama perusahaan/instansi tempat bekerja)
        $instansiMap = DB::table('answers as a')
            ->join('answer_details as ad', 'ad.answer_id', '=', 'a.id')
            ->join('questions as q', 'q.id', '=', 'a.question_id')
            ->where('q.kode_soal', 'f5b')
            ->whereIn('a.alumni_id', $alumniIds)
            ->select('a.alumni_id', 'ad.value as nama_instansi')
            ->get()
            ->keyBy('alumni_id');

        // Ambil jawaban f5c (posisi/jabatan)
        $posisiMap = DB::table('answers as a')
            ->join('answer_details as ad', 'ad.answer_id', '=', 'a.id')
            ->join('questions as q', 'q.id', '=', 'a.question_id')
            ->join('question_options as qo', 'qo.id', '=', 'ad.option_id')
            ->where('q.kode_soal', 'f5c')
            ->whereIn('a.alumni_id', $alumniIds)
            ->select('a.alumni_id', 'qo.label as posisi')
            ->get()
            ->keyBy('alumni_id');

        return $alumni->map(function ($item) use ($statusMap, $instansiMap, $posisiMap) {
            $item->status_pekerjaan = $statusMap[$item->id]->status_pekerjaan ?? '-';
            $item->nama_instansi    = $instansiMap[$item->id]->nama_instansi ?? '-';
            $item->posisi           = $posisiMap[$item->id]->posisi ?? '-';
            return $item;
        });
    }

    /**
     * =========================================
     * GENERATE EXCEL FULL (sudah mengisi / semua)
     * Kolom: Nama, NIM, Prodi, No HP, Email, Alamat,
     *        Tempat Lahir, Tanggal Lahir, Angkatan, Tahun Lulus,
     *        Status Pekerjaan, Nama Instansi
     * =========================================
     */
    private function generateExcel($data, $title, $fileName)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->mergeCells('A1:M1');
        $sheet->setCellValue('A1', $title);
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Header
        $headings = [
            'Nama', 'NIM', 'Program Studi', 'No HP', 'Email', 'Alamat',
            'Tempat Lahir', 'Tanggal Lahir', 'Angkatan', 'Tahun Lulus',
            'Status Pekerjaan', 'Nama Instansi', 'Posisi',
        ];

        foreach ($headings as $col => $heading) {
            $column = Coordinate::stringFromColumnIndex($col + 1);
            $sheet->setCellValue($column . '3', $heading);
        }

        $lastCol = Coordinate::stringFromColumnIndex(count($headings));

        $sheet->getStyle("A3:{$lastCol}3")->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        foreach (range(1, count($headings)) as $index) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($index))->setAutoSize(true);
        }

        // Data
        $row = 4;
        foreach ($data as $item) {
            $sheet->setCellValue("A$row", $item->nama);
            $sheet->setCellValue("B$row", $item->nim);
            $sheet->setCellValue("C$row", $item->prodi);
            $sheet->setCellValue("D$row", $item->no_hp);
            $sheet->setCellValue("E$row", $item->email);
            $sheet->setCellValue("F$row", $item->alamat);
            $sheet->setCellValue("G$row", $item->tempat_lahir ?? '-');
            $sheet->setCellValue("H$row", $item->tanggal_lahir ? \Carbon\Carbon::parse($item->tanggal_lahir)->format('d-m-Y') : '-');
            $sheet->setCellValue("I$row", $item->angkatan);
            $sheet->setCellValue("J$row", $item->tahun_lulus);
            $sheet->setCellValue("K$row", $item->status_pekerjaan);
            $sheet->setCellValue("L$row", $item->nama_instansi);
            $sheet->setCellValue("M$row", $item->posisi);
            $row++;
        }

        $lastRow = $row - 1;
        $sheet->getStyle("A4:{$lastCol}{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        return $this->downloadExcel($spreadsheet, $fileName);
    }

    /**
     * =========================================
     * GENERATE EXCEL BELUM MENGISI
     * Kolom: Nama, NIM, Prodi, No HP, Email, Alamat,
     *        Tempat Lahir, Tanggal Lahir, Angkatan, Tahun Lulus
     * =========================================
     */
    private function generateExcelBelumMengisi($data, $title, $fileName)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', $title);
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Header
        $headings = [
            'Nama', 'NIM', 'Program Studi', 'No HP', 'Email', 'Alamat',
            'Tempat Lahir', 'Tanggal Lahir', 'Angkatan', 'Tahun Lulus',
        ];

        foreach ($headings as $col => $heading) {
            $column = Coordinate::stringFromColumnIndex($col + 1);
            $sheet->setCellValue($column . '3', $heading);
        }

        $lastCol = Coordinate::stringFromColumnIndex(count($headings));

        $sheet->getStyle("A3:{$lastCol}3")->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C00000'],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        foreach (range(1, count($headings)) as $index) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($index))->setAutoSize(true);
        }

        // Data
        $row = 4;
        foreach ($data as $item) {
            $sheet->setCellValue("A$row", $item->nama);
            $sheet->setCellValue("B$row", $item->nim);
            $sheet->setCellValue("C$row", $item->prodi);
            $sheet->setCellValue("D$row", $item->no_hp);
            $sheet->setCellValue("E$row", $item->email);
            $sheet->setCellValue("F$row", $item->alamat);
            $sheet->setCellValue("G$row", $item->tempat_lahir ?? '-');
            $sheet->setCellValue("H$row", $item->tanggal_lahir ? \Carbon\Carbon::parse($item->tanggal_lahir)->format('d-m-Y') : '-');
            $sheet->setCellValue("I$row", $item->angkatan);
            $sheet->setCellValue("J$row", $item->tahun_lulus);
            $row++;
        }

        $lastRow = $row - 1;
        $sheet->getStyle("A4:{$lastCol}{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        return $this->downloadExcel($spreadsheet, $fileName);
    }

    /**
     * =========================================
     * HELPER: Download Excel
     * =========================================
     */
    private function downloadExcel(Spreadsheet $spreadsheet, string $fileName)
    {
        $writer   = new Xlsx($spreadsheet);
        $filename = $fileName . '_' . date('d-m-Y_H-i') . '.xlsx';

        return response()->streamDownload(
            function () use ($writer) {
                $writer->save('php://output');
            },
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }
}
