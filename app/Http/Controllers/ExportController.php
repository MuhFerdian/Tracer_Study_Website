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
        $data = alumniModel::select([
                'nama',
                'nim',
                'prodi',
                'no_hp',
                'email',
                'alamat',
                'angkatan',
                'tahun_lulus',
                'status_pekerjaan',
                'nama_instansi',
                'posisi',
            ])
            ->orderBy('id')
            ->get();

        if ($data->isEmpty()) {

        return redirect()
            ->back()
            ->with(
                'error',
                'Data alumni tracer study masih kosong!'
            );
        }

        return $this->generateExcel(
            $data,
            'DATA ALUMNI TRACER STUDY',
            'data_alumni'
        );
    }

    /**
     * =========================================
     * EXPORT ALUMNI SUDAH MENGISI
     * =========================================
     */
    public function exportExcelSudahMengisi()
    {
        $data = alumniModel::whereIn(
                'id',
                DB::table('answers')
                    ->select('alumni_id')
                    ->distinct()
            )
            ->select([
                'nama',
                'nim',
                'prodi',
                'no_hp',
                'email',
                'alamat',
                'angkatan',
                'tahun_lulus',
                'status_pekerjaan',
                'nama_instansi',
                'posisi',
            ])
            ->orderBy('id')
            ->get();

        if ($data->isEmpty()) {

        return redirect()
            ->back()
            ->with(
                'error',
                'Data alumni yang sudah mengisi tracer study masih kosong!'
            );
        }

        return $this->generateExcel(
            $data,
            'DATA ALUMNI SUDAH MENGISI TRACER STUDY',
            'alumni_sudah_mengisi'
        );
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
                'nama',
                'nim',
                'prodi',
                'no_hp',
                'email',
                'alamat',
                'angkatan',
                'tahun_lulus',
            ])
            ->orderBy('id')
            ->get();

        if ($data->isEmpty()) {

        return redirect()
            ->back()
            ->with(
                'error',
                'Data alumni yang belum mengisi tracer study masih kosong!'
            );
        }

        return $this->generateExcelBelumMengisi(
            $data,
            'DATA ALUMNI BELUM MENGISI TRACER STUDY',
            'alumni_belum_mengisi'
        );
    }

    /**
     * =========================================
     * GENERATE EXCEL FULL
     * =========================================
     */
    private function generateExcel($data, $title, $fileName)
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        /**
         * =========================================
         * JUDUL
         * =========================================
         */
        $sheet->mergeCells('A1:K1');

        $sheet->setCellValue('A1', $title);

        $sheet->getStyle('A1')->applyFromArray([

            'font' => [
                'bold' => true,
                'size' => 16,
            ],

            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);

        /**
         * =========================================
         * HEADER
         * =========================================
         */
        $headings = [

            'Nama',
            'NIM',
            'Program Studi',
            'No HP',
            'Email',
            'Alamat',
            'Angkatan',
            'Tahun Lulus',
            'Status Pekerjaan',
            'Nama Instansi',
            'Posisi',
        ];

        $headerRow = 3;

        foreach ($headings as $col => $heading) {

            $column =
                Coordinate::stringFromColumnIndex($col + 1);

            $sheet->setCellValue(
                $column . $headerRow,
                $heading
            );
        }

        /**
         * =========================================
         * STYLE HEADER
         * =========================================
         */
        $sheet->getStyle("A3:K3")->applyFromArray([

            'font' => [
                'bold' => true,
                'color' => [
                    'rgb' => 'FFFFFF'
                ],
                'size' => 11
            ],

            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '1F4E78'
                ]
            ],

            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],

            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);

        /**
         * =========================================
         * AUTO SIZE
         * =========================================
         */
        foreach (range(1, count($headings)) as $index) {

            $column =
                Coordinate::stringFromColumnIndex($index);

            $sheet->getColumnDimension($column)
                ->setAutoSize(true);
        }

        /**
         * =========================================
         * ISI DATA
         * =========================================
         */
        $row = 4;

        foreach ($data as $item) {

            $sheet->setCellValue("A$row", $item->nama);
            $sheet->setCellValue("B$row", $item->nim);
            $sheet->setCellValue("C$row", $item->prodi);
            $sheet->setCellValue("D$row", $item->no_hp);
            $sheet->setCellValue("E$row", $item->email);
            $sheet->setCellValue("F$row", $item->alamat);
            $sheet->setCellValue("G$row", $item->angkatan);
            $sheet->setCellValue("H$row", $item->tahun_lulus);
            $sheet->setCellValue("I$row", $item->status_pekerjaan);
            $sheet->setCellValue("J$row", $item->nama_instansi);
            $sheet->setCellValue("K$row", $item->posisi);

            $row++;
        }

        $lastRow = $row - 1;

        /**
         * =========================================
         * STYLE DATA
         * =========================================
         */
        $sheet->getStyle("A4:K$lastRow")->applyFromArray([

            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);

        /**
         * =========================================
         * DOWNLOAD
         * =========================================
         */
        $writer = new Xlsx($spreadsheet);

        $filename =
            $fileName . '_' .
            date('d-m-Y_H-i') .
            '.xlsx';

        return response()->streamDownload(

            function () use ($writer) {
                $writer->save('php://output');
            },

            $filename,

            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }

    /**
     * =========================================
     * GENERATE EXCEL BELUM MENGISI
     * =========================================
     */
    private function generateExcelBelumMengisi($data, $title, $fileName)
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:H1');

        $sheet->setCellValue('A1', $title);

        $sheet->getStyle('A1')->applyFromArray([

            'font' => [
                'bold' => true,
                'size' => 16,
            ],

            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);

        $headings = [

            'Nama',
            'NIM',
            'Program Studi',
            'No HP',
            'Email',
            'Alamat',
            'Angkatan',
            'Tahun Lulus',
        ];

        foreach ($headings as $col => $heading) {

            $column =
                Coordinate::stringFromColumnIndex($col + 1);

            $sheet->setCellValue(
                $column . '3',
                $heading
            );
        }

        $sheet->getStyle("A3:H3")->applyFromArray([

            'font' => [
                'bold' => true,
                'color' => [
                    'rgb' => 'FFFFFF'
                ],
            ],

            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'C00000'
                ]
            ],

            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],

            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);

        foreach (range(1, count($headings)) as $index) {

            $column =
                Coordinate::stringFromColumnIndex($index);

            $sheet->getColumnDimension($column)
                ->setAutoSize(true);
        }

        $row = 4;

        foreach ($data as $item) {

            $sheet->setCellValue("A$row", $item->nama);
            $sheet->setCellValue("B$row", $item->nim);
            $sheet->setCellValue("C$row", $item->prodi);
            $sheet->setCellValue("D$row", $item->no_hp);
            $sheet->setCellValue("E$row", $item->email);
            $sheet->setCellValue("F$row", $item->alamat);
            $sheet->setCellValue("G$row", $item->angkatan);
            $sheet->setCellValue("H$row", $item->tahun_lulus);

            $row++;
        }

        $lastRow = $row - 1;

        $sheet->getStyle("A4:H$lastRow")->applyFromArray([

            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);

        $writer = new Xlsx($spreadsheet);

        $filename =
            $fileName . '_' .
            date('d-m-Y_H-i') .
            '.xlsx';

        return response()->streamDownload(

            function () use ($writer) {
                $writer->save('php://output');
            },

            $filename,

            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }
}