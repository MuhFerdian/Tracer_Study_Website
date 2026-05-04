<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Carbon;
use App\Models\alumniModel;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExportController extends Controller
{

    public function showAlumniBelumMengisi()
    {
        $alumni = alumniModel::where(function ($query) {
            $query->whereNull('status_pekerjaan')
                ->whereNull('nama_instansi')
                ->whereNull('posisi');
        })->get();
        return view('layoutAdmin.rekap.export_rekap_alumni_belum_mengisi', compact('alumni'));
    }
    public function showAlumni()
    {
        $alumni = alumniModel::where(function ($query) {
            $query->whereNotNull('status_pekerjaan')
                ->orWhereNotNull('nama_instansi')
                ->orWhereNotNull('posisi');
        })->get();
        return view('layoutAdmin.rekap.export_rekap_alumni', compact('alumni'));
    }

    // Tambahkan ini
    public function collection()
    {
        return alumniModel::query()
            ->select('prodi as program_studi', 'nim', 'nama', 'tahun_lulus')
            ->get();
    }

    // Tambahkan ini
    public function headings(): array
    {
        return [
            'Program Studi',
            'NIM',
            'Nama',
            'Tanggal Lulus',
        ];
    }

    public function exportExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header kolom
        $headings = $this->headings();
        foreach ($headings as $col => $heading) {
            $columnLetter = Coordinate::stringFromColumnIndex($col + 1); // Ubah angka ke huruf (1 => A, 2 => B, dst)
            $sheet->setCellValue($columnLetter . '1', $heading); // Misal: A1, B1, C1, ...
        }

        // Heading bold
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        // Atur lebar kolom agar tidak terlalu mepet
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);

        // Ambil data dari database
        $data = $this->collection();
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue("A$row", $item->program_studi);
            $sheet->setCellValue("B$row", $item->nim);
            $sheet->setCellValue("C$row", $item->nama);
            $sheet->setCellValue("D$row", $item->tahun_lulus);
            $row++;
        }

        // Terapkan rata kiri ke semua kolom dari A2 sampai D<lastRow>
        $sheet->getStyle("A2:D" . ($row - 1))
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        // Siapkan file untuk download
        $writer = new Xlsx($spreadsheet);
        $filename = 'alumni.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
    public function exportExcelLulusan()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $headings = [
            'Program Studi',
            'NIM',
            'Nama Alumni',
            'No HP',
            'Email',
            'Tanggal Lulus',
            'Tahun Lulus',
            'Tanggal Kerja Pertama',
            'Masa Tunggu (hitung)',
            'Masa Tunggu (tersimpan)',
            'Tanggal Mulai Instansi',
            'Jenis Instansi',
            'Nama Instansi',
            'Skala Instansi',
            'Lokasi Instansi',
            'Kategori Profesi',
            'Profesi',
            'Nama Atasan',
            'Jabatan Atasan',
            'No HP Atasan',
            'Email Atasan'
        ];

        foreach ($headings as $col => $heading) {
            $columnLetter = Coordinate::stringFromColumnIndex($col + 1);
            $sheet->setCellValue($columnLetter . '1', $heading);
        }

        $sheet->getStyle('A1:U1')->getFont()->setBold(true);

        // Atur lebar kolom
        foreach (range(1, count($headings)) as $index) {
            $colLetter = Coordinate::stringFromColumnIndex($index);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Query data
        $data = DB::table('alumni as al')
            ->orderBy('al.id')
            ->selectRaw('
            al.prodi,
            al.nim,
            al.nama,
            al.no_hp,
            al.email,
            al.tahun_lulus,
            al.status_pekerjaan,
            al.nama_instansi,
            al.posisi
        ')
            ->get();

        // Isi data ke Excel
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue("A$row", $item->prodi);
            $sheet->setCellValue("B$row", $item->nim);
            $sheet->setCellValue("C$row", $item->nama);
            $sheet->setCellValue("D$row", $item->no_hp);
            $sheet->setCellValue("E$row", $item->email);
            $sheet->setCellValue("F$row", $item->tahun_lulus);
            $sheet->setCellValue("G$row", $item->status_pekerjaan);
            $sheet->setCellValue("H$row", $item->nama_instansi);
            $sheet->setCellValue("I$row", $item->posisi);
            $row++;
        }

        // Rata kiri semua isi data
        $sheet->getStyle("A2:I" . ($row - 1))
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        // Download
        $writer = new Xlsx($spreadsheet);
        $filename = 'rekap_alumni.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}