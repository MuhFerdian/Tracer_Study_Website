<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PertanyaanReferenceController extends Controller
{
    /**
     * Tampilkan halaman reference pertanyaan wajib Kemendikbud
     */
    public function index()
    {
        $pertanyaanWajib = [
            [
                'no' => 1,
                'kategori' => 'Identitas',
                'kode' => '-',
                'pertanyaan' => 'Biodata Alumni (NIM, Nama, Email, dll)',
                'tipe' => 'Text / Identitas',
                'deskripsi' => 'Data personal alumni sebagai identitas'
            ],
            [
                'no' => 2,
                'kategori' => 'Status Pekerjaan',
                'kode' => 'f8',
                'pertanyaan' => 'Jelaskan status Anda saat ini?',
                'tipe' => 'Single Choice',
                'deskripsi' => 'Pilihan: Bekerja, Belum memungkinkan, Wiraswasta, Melanjutkan Pendidikan, Tidak kerja tapi mencari'
            ],
            [
                'no' => 3,
                'kategori' => 'Lama Bekerja',
                'kode' => 'f502',
                'pertanyaan' => 'Dalam berapa bulan Anda mendapatkan pekerjaan pertama?',
                'tipe' => 'Number (Bulan)',
                'deskripsi' => 'Hanya jika memilih "Bekerja" pada f8'
            ],
            [
                'no' => 4,
                'kategori' => 'Lama Bekerja',
                'kode' => 'f503',
                'pertanyaan' => 'Dalam berapa bulan setelah lulus Anda memulai wiraswasta?',
                'tipe' => 'Number (Bulan)',
                'deskripsi' => 'Hanya jika memilih "Wiraswasta" pada f8'
            ],
            [
                'no' => 5,
                'kategori' => 'Pendapatan',
                'kode' => 'f505',
                'pertanyaan' => 'Berapa rata-rata pendapatan Anda per bulan? (take home pay)',
                'tipe' => 'Number (Rupiah)',
                'deskripsi' => 'Pendapatan bersih bulanan'
            ],
            [
                'no' => 6,
                'kategori' => 'Lokasi Bekerja',
                'kode' => 'f5a1 / f5a2',
                'pertanyaan' => 'Di mana lokasi tempat Anda bekerja? (Provinsi & Kota/Kabupaten)',
                'tipe' => 'Text',
                'deskripsi' => 'Lokasi geografis tempat bekerja'
            ],
            [
                'no' => 7,
                'kategori' => 'Jenis Perusahaan',
                'kode' => 'f1101',
                'pertanyaan' => 'Apa jenis perusahaan/instansi/institusi tempat Anda bekerja sekarang?',
                'tipe' => 'Single Choice',
                'deskripsi' => 'Pilihan: Pemerintah, Non-profit, Swasta, Wiraswasta, BUMN/BUMD, Multilateral, Lainnya'
            ],
            [
                'no' => 8,
                'kategori' => 'Jenis Perusahaan',
                'kode' => 'f1102',
                'pertanyaan' => 'Tuliskan jenis perusahaan/instansi/institusi lainnya',
                'tipe' => 'Text',
                'deskripsi' => 'Hanya jika memilih "Lainnya" pada f1101'
            ],
            [
                'no' => 9,
                'kategori' => 'Nama Perusahaan',
                'kode' => 'f5b',
                'pertanyaan' => 'Apa nama perusahaan/kantor tempat Anda bekerja?',
                'tipe' => 'Text',
                'deskripsi' => 'Nama lengkap perusahaan/institusi'
            ],
            [
                'no' => 10,
                'kategori' => 'Posisi Bekerja',
                'kode' => 'f5c',
                'pertanyaan' => 'Bila berwiraswasta, apa posisi/jabatan Anda saat ini?',
                'tipe' => 'Single Choice',
                'deskripsi' => 'Pilihan: Founder, Co-Founder, Staff, Freelance/Lepas'
            ],
            [
                'no' => 11,
                'kategori' => 'Tingkat Perusahaan',
                'kode' => 'f5d',
                'pertanyaan' => 'Apa tingkat tempat kerja Anda?',
                'tipe' => 'Single Choice',
                'deskripsi' => 'Pilihan: Lokal/Wilayah, Nasional, Multinasional/Internasional'
            ],
            [
                'no' => 12,
                'kategori' => 'Pendidikan Lanjut',
                'kode' => 'f18a',
                'pertanyaan' => 'Sumber biaya studi lanjut',
                'tipe' => 'Single Choice',
                'deskripsi' => 'Pilihan: Biaya Sendiri, Beasiswa'
            ],
            [
                'no' => 13,
                'kategori' => 'Pembiayaan Kuliah',
                'kode' => 'f1201',
                'pertanyaan' => 'Sebutkan sumber dana dalam pembiayaan kuliah?',
                'tipe' => 'Single Choice *Wajib',
                'deskripsi' => 'Pilihan: Biaya Sendiri, ADIK, BIDIKMISI, PPA, AFIRMASI, Perusahaan/Swasta, Lainnya'
            ],
            [
                'no' => 14,
                'kategori' => 'Kesesuaian Bidang',
                'kode' => 'f14',
                'pertanyaan' => 'Seberapa erat hubungan bidang studi dengan pekerjaan Anda?',
                'tipe' => 'Single Choice *Wajib',
                'deskripsi' => 'Pilihan: Sangat Erat, Erat, Cukup Erat, Kurang Erat, Tidak Sama Sekali'
            ],
            [
                'no' => 15,
                'kategori' => 'Tingkat Pendidikan',
                'kode' => 'f15',
                'pertanyaan' => 'Tingkat pendidikan apa yang paling tepat untuk pekerjaan Anda?',
                'tipe' => 'Single Choice *Wajib',
                'deskripsi' => 'Pilihan: Lebih Tinggi, Sama, Lebih Rendah, Tidak Perlu'
            ],
            [
                'no' => 16,
                'kategori' => 'Kompetensi (Matrix) *Wajib',
                'kode' => 'f1761-f1774',
                'pertanyaan' => 'Pada saat lulus & sekarang - Tingkat kompetensi (Etika, Keahlian, B.Inggris, IT, Komunikasi, Tim, Pengembangan)',
                'tipe' => 'Matrix / Scale 1-5 *Wajib',
                'deskripsi' => 'Tabel dengan sub-items untuk setiap kompetensi'
            ],
            [
                'no' => 17,
                'kategori' => 'Metode Pembelajaran',
                'kode' => 'f21-f27',
                'pertanyaan' => 'Penekanan metode pembelajaran (Perkuliahan, Demonstrasi, Riset, Magang, Praktikum, Lapangan, Diskusi)',
                'tipe' => 'Single Choice (7 pertanyaan)',
                'deskripsi' => 'Skala: Sangat Besar, Besar, Cukup, Kurang, Tidak Sama Sekali'
            ],
            [
                'no' => 18,
                'kategori' => 'Pencarian Kerja',
                'kode' => 'f301-f303',
                'pertanyaan' => 'Kapan Anda mulai mencari pekerjaan? (Bulan sebelum/sesudah lulus)',
                'tipe' => 'Single Choice + Number',
                'deskripsi' => 'Timing pencarian kerja'
            ],
            [
                'no' => 19,
                'kategori' => 'Cara Mencari Kerja',
                'kode' => 'f401-f415',
                'pertanyaan' => 'Bagaimana Anda mencari pekerjaan tersebut? (Bisa lebih dari satu)',
                'tipe' => 'Multiple Choice',
                'deskripsi' => 'Pilihan: Iklan, Relasi, Network, Internet, Lamaran, dll (15 opsi)'
            ],
            [
                'no' => 20,
                'kategori' => 'Jumlah Lamaran',
                'kode' => 'f6-f7a',
                'pertanyaan' => 'Berapa banyak perusahaan yang dilamar, merespons, dan wawancara?',
                'tipe' => 'Number',
                'deskripsi' => '3 pertanyaan untuk tracking efisiensi pencarian kerja'
            ],
            [
                'no' => 21,
                'kategori' => 'Status Pencarian Kerja',
                'kode' => 'f1001',
                'pertanyaan' => 'Apakah Anda aktif mencari pekerjaan dalam 4 minggu terakhir?',
                'tipe' => 'Single Choice',
                'deskripsi' => 'Status aktivitas pencarian terkini'
            ],
            [
                'no' => 22,
                'kategori' => 'Kesesuaian Pekerjaan',
                'kode' => 'f1601-f1613',
                'pertanyaan' => 'Jika pekerjaan tidak sesuai, mengapa mengambilnya? (Bisa >1)',
                'tipe' => 'Multiple Choice',
                'deskripsi' => 'Pilihan: Prospek, Pendapatan, Keamanan, Lokasi, dll (13 opsi)'
            ],
        ];

        return view('layoutAdmin.pertanyaan.reference', compact('pertanyaanWajib'));
    }

    /**
     * Download template Excel untuk bulk import
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Pertanyaan');

        // Header row styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];

        // Set column headers
        $headers = ['Kode Soal', 'Pertanyaan', 'Tipe', 'Keterangan', 'Options (JSON)', 'Urutan'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F'];
        
        foreach ($headers as $idx => $header) {
            $sheet->setCellValue($columns[$idx] . '1', $header);
            $sheet->getStyle($columns[$idx] . '1')->applyFromArray($headerStyle);
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15);  // Kode Soal
        $sheet->getColumnDimension('B')->setWidth(40);  // Pertanyaan
        $sheet->getColumnDimension('C')->setWidth(18);  // Tipe
        $sheet->getColumnDimension('D')->setWidth(30);  // Keterangan
        $sheet->getColumnDimension('E')->setWidth(35);  // Options
        $sheet->getColumnDimension('F')->setWidth(10);  // Urutan

        // Add example data
        $examples = [
            ['f8', 'Jelaskan status Anda saat ini?', 'single', 'Status pekerjaan alumni', '["Bekerja","Wiraswasta","Melanjutkan Pendidikan"]', '1'],
            ['f502', 'Dalam berapa bulan Anda mendapatkan pekerjaan pertama?', 'text', 'Lama mencari kerja', '[]', '2'],
            ['f1761', 'Etika (saat lulus vs sekarang)', 'scale', 'Kompetensi - Etika', '[]', '3'],
        ];

        $row = 2;
        foreach ($examples as $example) {
            foreach ($example as $colIdx => $value) {
                $sheet->setCellValue($columns[$colIdx] . $row, $value);
            }
            $row++;
        }

        // Add instructions sheet
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Panduan');
        
        $instructions = [
            ['PANDUAN IMPORT TEMPLATE PERTANYAAN'],
            [''],
            ['KOLOM:'],
            ['1. Kode Soal: Kode unik pertanyaan (contoh: f8, f502, f1761)'],
            ['2. Pertanyaan: Teks pertanyaan yang akan ditanyakan'],
            ['3. Tipe: single, multiple, text, scale, matrix'],
            ['4. Keterangan: Deskripsi atau petunjuk untuk alumni'],
            ['5. Options (JSON): Format array JSON untuk pilihan ganda/single (contoh: ["Opsi 1","Opsi 2"])'],
            ['6. Urutan: Nomor urutan pertanyaan dalam survey'],
            [''],
            ['CATATAN:'],
            ['- Gunakan format JSON yang valid untuk kolom Options'],
            ['- Tipe "scale" otomatis 1-5, tidak perlu options'],
            ['- Tipe "text" tidak memerlukan options'],
            ['- Pastikan kode soal unik di dalam database'],
        ];

        $instructionRow = 1;
        foreach ($instructions as $instruction) {
            $instructionSheet->setCellValue('A' . $instructionRow, $instruction[0]);
            $instructionRow++;
        }
        $instructionSheet->getColumnDimension('A')->setWidth(80);

        // Create file and download
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Template_Pertanyaan_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Import pertanyaan dari file Excel
     */
    public function importQuestions(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        try {
            $file = $request->file('file');
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $imported = 0;
            $errors = [];

            // Skip header row
            for ($i = 2; $i <= count($rows); $i++) {
                $row = $rows[$i - 1];
                
                // Skip empty rows
                if (empty($row[0]) && empty($row[1])) continue;

                try {
                    $kode_soal = trim($row[0] ?? '');
                    $pertanyaan = trim($row[1] ?? '');
                    $tipe = trim($row[2] ?? '');
                    $keterangan = trim($row[3] ?? '');
                    $options_json = trim($row[4] ?? '[]');
                    $urutan = intval($row[5] ?? 1);

                    // Validasi
                    if (empty($pertanyaan)) {
                        $errors[] = "Baris $i: Pertanyaan tidak boleh kosong";
                        continue;
                    }

                    if (!in_array($tipe, ['text', 'single', 'multiple', 'scale', 'matrix'])) {
                        $errors[] = "Baris $i: Tipe '$tipe' tidak valid";
                        continue;
                    }

                    // Parse options
                    $options = [];
                    if ($tipe !== 'text' && $tipe !== 'scale') {
                        $options = json_decode($options_json, true);
                        if (!is_array($options)) {
                            $errors[] = "Baris $i: Format options JSON tidak valid";
                            continue;
                        }
                    }

                    // Handle duplicate urutan - find next available
                    $maxUrutan = Question::max('urutan') ?? 0;
                    if ($urutan <= $maxUrutan) {
                        // Check if urutan already exists
                        $existingUrutan = Question::where('urutan', $urutan)->first();
                        if ($existingUrutan && (!$kode_soal || $existingUrutan->kode_soal !== $kode_soal)) {
                            // Use next available urutan
                            $urutan = $maxUrutan + 1;
                        }
                    }

                    // Create or update question
                    $question = Question::updateOrCreate(
                        ['kode_soal' => $kode_soal ?: null],
                        [
                            'pertanyaan' => $pertanyaan,
                            'type' => $tipe,
                            'hint' => $keterangan,
                            'urutan' => $urutan,
                        ]
                    );

                    // Delete existing options and create new ones
                    $question->options()->delete();
                    foreach ($options as $idx => $option) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'label' => $option,
                            'value' => $idx + 1,
                            'urutan' => $idx + 1,
                        ]);
                    }

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris $i: " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "$imported pertanyaan berhasil diimport",
                'imported' => $imported,
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal import file: ' . $e->getMessage(),
            ], 422);
        }
    }
}
