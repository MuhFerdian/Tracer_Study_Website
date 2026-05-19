<?php
namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
                'pertanyaan' => 'Biodata Alumni (Nama, NIM, Email, Tahun Lulus, Prodi, Jurusan, Alamat, NIK, NPWP)',
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

        // Set column headers — tambah kolom Tipe Data
        $headers = ['Kode Soal', 'Pertanyaan', 'Tipe', 'Tipe Data', 'Keterangan', 'Options (JSON)', 'Urutan'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        
        foreach ($headers as $idx => $header) {
            $sheet->setCellValue($columns[$idx] . '1', $header);
            $sheet->getStyle($columns[$idx] . '1')->applyFromArray($headerStyle);
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15);  // Kode Soal
        $sheet->getColumnDimension('B')->setWidth(40);  // Pertanyaan
        $sheet->getColumnDimension('C')->setWidth(18);  // Tipe
        $sheet->getColumnDimension('D')->setWidth(12);  // Tipe Data
        $sheet->getColumnDimension('E')->setWidth(30);  // Keterangan
        $sheet->getColumnDimension('F')->setWidth(35);  // Options
        $sheet->getColumnDimension('G')->setWidth(10);  // Urutan

        // 52 pertanyaan lengkap sesuai instrumen Kemendikbud
        // Format: [kode_soal, pertanyaan, tipe, tipe_data, keterangan, options_json, urutan]
        $scaleOpts = '["Sangat Rendah","Rendah","Sedang","Tinggi","Sangat Tinggi"]';
        $learningOpts = '["Sangat Besar","Besar","Cukup Besar","Kurang Besar","Tidak Sama Sekali"]';

        $examples = [
            ['f8',   'Jelaskan status Anda saat ini?', 'single', 'text',
             'Pilih satu status yang paling sesuai kondisi Anda saat ini setelah lulus.',
             '["Bekerja (full time / part time)","Belum memungkinkan bekerja","Wiraswasta","Melanjutkan Pendidikan","Tidak kerja tetapi sedang mencari kerja"]', '1'],

            ['f502', 'Dalam berapa bulan Anda mendapatkan pekerjaan pertama? (Jika memilih Bekerja)', 'text', 'number',
             'Hitung dari bulan kelulusan hingga mulai bekerja. Isi angka, contoh: 3', '[]', '2'],

            ['f503', 'Dalam berapa bulan setelah lulus Anda memulai wiraswasta? (Jika memilih Wiraswasta)', 'text', 'number',
             'Hitung dari bulan kelulusan hingga memulai usaha. Isi angka.', '[]', '3'],

            ['f505', 'Berapa rata-rata pendapatan Anda per bulan? (take home pay)', 'text', 'number',
             'Isi dengan total pendapatan bersih per bulan dalam Rupiah. Contoh: 5000000', '[]', '4'],

            ['f5a1', 'Di mana lokasi tempat Anda bekerja? Provinsi', 'text', 'text',
             'Tuliskan nama provinsi tempat Anda bekerja. Contoh: Jawa Timur', '[]', '5'],

            ['f5a2', 'Di mana lokasi tempat Anda bekerja? Kota/Kabupaten', 'text', 'text',
             'Tuliskan nama kota atau kabupaten tempat Anda bekerja. Contoh: Kota Malang', '[]', '6'],

            ['f1101', 'Apa jenis perusahaan/instansi/institusi tempat Anda bekerja sekarang?', 'single', 'text',
             'Pilih jenis instansi/perusahaan tempat Anda bekerja saat ini.',
             '["Instansi pemerintah","Organisasi non-profit/Lembaga Swadaya Masyarakat","Perusahaan swasta","Wiraswasta/perusahaan sendiri","BUMN/BUMD","Institusi/Organisasi Multilateral","Lainnya, tuliskan"]', '7'],

            ['f1102', 'Tuliskan jenis perusahaan/instansi/institusi lainnya', 'text', 'text',
             'Isi jika memilih "Lainnya" pada pertanyaan sebelumnya.', '[]', '8'],

            ['f5b', 'Apa nama perusahaan/kantor tempat Anda bekerja?', 'text', 'text',
             'Tuliskan nama lengkap perusahaan, instansi, atau organisasi tempat Anda bekerja.', '[]', '9'],

            ['f5c', 'Bila berwiraswasta, apa posisi/jabatan Anda saat ini?', 'single', 'text',
             'Pilih posisi/jabatan Anda dalam usaha yang Anda jalankan.',
             '["Founder","Co-Founder","Staff","Freelance/Kerja lepas"]', '10'],

            ['f5d', 'Apa tingkat tempat kerja Anda?', 'single', 'text',
             'Pilih skala operasional tempat Anda bekerja.',
             '["Lokal/wilayah/wiraswasta tidak berbadan hukum","Nasional/wiraswasta berbadan hukum","Multinasional/internasional"]', '11'],

            ['f18a', 'Pertanyaan studi lanjut: sumber biaya', 'single', 'text',
             'Jawab jika Anda memilih "Melanjutkan Pendidikan" pada pertanyaan status.',
             '["Biaya sendiri","Beasiswa"]', '12'],

            ['f18b', 'Pertanyaan studi lanjut: perguruan tinggi', 'text', 'text',
             'Tuliskan nama perguruan tinggi tempat Anda melanjutkan studi.', '[]', '13'],

            ['f18c', 'Pertanyaan studi lanjut: program studi', 'text', 'text',
             'Tuliskan nama program studi yang Anda ambil.', '[]', '14'],

            ['f18d', 'Pertanyaan studi lanjut: tanggal masuk', 'text', 'date',
             'Pilih tanggal Anda mulai masuk/terdaftar di perguruan tinggi tersebut.', '[]', '15'],

            ['f1201', 'Sebutkan sumber dana dalam pembiayaan kuliah? (bukan ketika Studi Lanjut)', 'single', 'text',
             'Pilih sumber dana utama yang membiayai kuliah Anda.',
             '["Biaya Sendiri/Keluarga","Beasiswa ADIK","Beasiswa BIDIKMISI","Beasiswa PPA","Beasiswa AFIRMASI","Beasiswa Perusahaan/Swasta","Lainnya, tuliskan"]', '16'],

            ['f1202', 'Tuliskan sumber dana lainnya dalam pembiayaan kuliah', 'text', 'text',
             'Isi jika memilih "Lainnya" pada pertanyaan sebelumnya.', '[]', '17'],

            ['f14', 'Seberapa erat hubungan bidang studi dengan pekerjaan Anda?', 'single', 'text',
             'Nilai seberapa erat keterkaitan antara bidang ilmu yang Anda pelajari dengan pekerjaan Anda.',
             '["Sangat Erat","Erat","Cukup Erat","Kurang Erat","Tidak Sama Sekali"]', '18'],

            ['f15', 'Tingkat pendidikan apa yang paling tepat/sesuai untuk pekerjaan Anda saat ini?', 'single', 'text',
             'Menurut Anda, tingkat pendidikan apa yang paling tepat untuk posisi pekerjaan Anda saat ini?',
             '["Setingkat Lebih Tinggi","Tingkat yang Sama","Setingkat Lebih Rendah","Tidak Perlu Pendidikan Tinggi"]', '19'],

            ['f1761', 'Pada saat lulus, pada tingkat mana kompetensi Etika Anda kuasai?', 'single', 'text',
             'Nilai tingkat penguasaan kompetensi Etika Anda pada saat lulus kuliah. (1=Sangat Rendah, 5=Sangat Tinggi)', $scaleOpts, '20'],
            ['f1762', 'Pada saat ini, pada tingkat mana kompetensi Etika diperlukan dalam pekerjaan?', 'single', 'text',
             'Nilai seberapa penting kompetensi Etika dalam pekerjaan Anda saat ini. (1=Sangat Rendah, 5=Sangat Tinggi)', $scaleOpts, '21'],
            ['f1763', 'Pada saat lulus, pada tingkat mana kompetensi Keahlian berdasarkan bidang ilmu Anda kuasai?', 'single', 'text',
             'Nilai tingkat penguasaan Keahlian bidang ilmu Anda pada saat lulus. (1=Sangat Rendah, 5=Sangat Tinggi)', $scaleOpts, '22'],
            ['f1764', 'Pada saat ini, pada tingkat mana kompetensi Keahlian berdasarkan bidang ilmu diperlukan dalam pekerjaan?', 'single', 'text',
             'Nilai seberapa penting Keahlian bidang ilmu dalam pekerjaan Anda. (1=Sangat Rendah, 5=Sangat Tinggi)', $scaleOpts, '23'],
            ['f1765', 'Pada saat lulus, pada tingkat mana kompetensi Bahasa Inggris Anda kuasai?', 'single', 'text',
             'Nilai tingkat penguasaan Bahasa Inggris Anda pada saat lulus. (1=Sangat Rendah, 5=Sangat Tinggi)', $scaleOpts, '24'],
            ['f1766', 'Pada saat ini, pada tingkat mana kompetensi Bahasa Inggris diperlukan dalam pekerjaan?', 'single', 'text',
             'Nilai seberapa penting Bahasa Inggris dalam pekerjaan Anda. (1=Sangat Rendah, 5=Sangat Tinggi)', $scaleOpts, '25'],
            ['f1767', 'Pada saat lulus, pada tingkat mana kompetensi Penggunaan Teknologi Informasi Anda kuasai?', 'single', 'text',
             'Nilai tingkat penguasaan Teknologi Informasi Anda pada saat lulus. (1=Sangat Rendah, 5=Sangat Tinggi)', $scaleOpts, '26'],
            ['f1768', 'Pada saat ini, pada tingkat mana kompetensi Penggunaan Teknologi Informasi diperlukan dalam pekerjaan?', 'single', 'text',
             'Nilai seberapa penting Teknologi Informasi dalam pekerjaan Anda. (1=Sangat Rendah, 5=Sangat Tinggi)', $scaleOpts, '27'],
            ['f1769', 'Pada saat lulus, pada tingkat mana kompetensi Komunikasi Anda kuasai?', 'single', 'text',
             'Nilai tingkat kemampuan Komunikasi Anda pada saat lulus. (1=Sangat Rendah, 5=Sangat Tinggi)', $scaleOpts, '28'],
            ['f1770', 'Pada saat ini, pada tingkat mana kompetensi Komunikasi diperlukan dalam pekerjaan?', 'single', 'text',
             'Nilai seberapa penting kemampuan Komunikasi dalam pekerjaan Anda. (1=Sangat Rendah, 5=Sangat Tinggi)', $scaleOpts, '29'],
            ['f1771', 'Pada saat lulus, pada tingkat mana kompetensi Kerja sama tim Anda kuasai?', 'single', 'text',
             'Nilai tingkat kemampuan Kerja sama tim Anda pada saat lulus. (1=Sangat Rendah, 5=Sangat Tinggi)', $scaleOpts, '30'],
            ['f1772', 'Pada saat ini, pada tingkat mana kompetensi Kerja sama tim diperlukan dalam pekerjaan?', 'single', 'text',
             'Nilai seberapa penting Kerja sama tim dalam pekerjaan Anda. (1=Sangat Rendah, 5=Sangat Tinggi)', $scaleOpts, '31'],
            ['f1773', 'Pada saat lulus, pada tingkat mana kompetensi Pengembangan diri Anda kuasai?', 'single', 'text',
             'Nilai tingkat kemampuan Pengembangan diri Anda pada saat lulus. (1=Sangat Rendah, 5=Sangat Tinggi)', $scaleOpts, '32'],
            ['f1774', 'Pada saat ini, pada tingkat mana kompetensi Pengembangan diri diperlukan dalam pekerjaan?', 'single', 'text',
             'Nilai seberapa penting Pengembangan diri dalam pekerjaan Anda. (1=Sangat Rendah, 5=Sangat Tinggi)', $scaleOpts, '33'],

            ['f21', 'Menurut Anda seberapa besar penekanan metode pembelajaran Perkuliahan dilaksanakan di program studi Anda?', 'single', 'text',
             'Nilai seberapa besar metode Perkuliahan (tatap muka di kelas) ditekankan dalam program studi Anda.', $learningOpts, '34'],
            ['f22', 'Menurut Anda seberapa besar penekanan metode pembelajaran Demonstrasi dilaksanakan di program studi Anda?', 'single', 'text',
             'Nilai seberapa besar metode Demonstrasi (peragaan langsung) ditekankan.', $learningOpts, '35'],
            ['f23', 'Menurut Anda seberapa besar penekanan Partisipasi dalam proyek riset dilaksanakan di program studi Anda?', 'single', 'text',
             'Nilai seberapa besar keterlibatan dalam Proyek Riset/Penelitian ditekankan.', $learningOpts, '36'],
            ['f24', 'Menurut Anda seberapa besar penekanan metode pembelajaran Magang dilaksanakan di program studi Anda?', 'single', 'text',
             'Nilai seberapa besar program Magang (kerja praktik di perusahaan/instansi) ditekankan.', $learningOpts, '37'],
            ['f25', 'Menurut Anda seberapa besar penekanan metode pembelajaran Praktikum dilaksanakan di program studi Anda?', 'single', 'text',
             'Nilai seberapa besar kegiatan Praktikum (di laboratorium atau studio) ditekankan.', $learningOpts, '38'],
            ['f26', 'Menurut Anda seberapa besar penekanan metode pembelajaran Kerja Lapangan dilaksanakan di program studi Anda?', 'single', 'text',
             'Nilai seberapa besar kegiatan Kerja Lapangan (survei, observasi, dll.) ditekankan.', $learningOpts, '39'],
            ['f27', 'Menurut Anda seberapa besar penekanan metode pembelajaran Diskusi dilaksanakan di program studi Anda?', 'single', 'text',
             'Nilai seberapa besar metode Diskusi (kelas, kelompok, seminar) ditekankan.', $learningOpts, '40'],

            ['f301', 'Kapan Anda mulai mencari pekerjaan? Mohon pekerjaan sambilan tidak dimasukkan', 'single', 'text',
             'Pilih kapan Anda mulai aktif mencari pekerjaan tetap (bukan pekerjaan sambilan/part-time).',
             '["Kira-kira bulan sebelum lulus","Kira-kira bulan sesudah lulus","Saya tidak mencari kerja"]', '41'],

            ['f302', 'Berapa bulan sebelum lulus Anda mulai mencari pekerjaan?', 'text', 'number',
             'Isi dengan jumlah bulan sebelum kelulusan Anda mulai mencari kerja. Contoh: 3', '[]', '42'],

            ['f303', 'Berapa bulan sesudah lulus Anda mulai mencari pekerjaan?', 'text', 'number',
             'Isi dengan jumlah bulan setelah kelulusan Anda mulai mencari kerja. Contoh: 2', '[]', '43'],

            ['f401-f416', 'Bagaimana Anda mencari pekerjaan tersebut? Jawaban bisa lebih dari satu', 'multiple', 'text',
             'Pilih semua cara yang pernah Anda gunakan untuk mencari pekerjaan pertama.',
             '["Melalui iklan di koran/majalah, brosur","Melamar ke perusahaan tanpa mengetahui lowongan yang ada","Pergi ke bursa/pameran kerja","Mencari lewat internet/iklan online/milis","Dihubungi oleh perusahaan","Menghubungi Kemenakertrans","Menghubungi agen tenaga kerja komersial/swasta","Memperoleh informasi dari pusat/kantor pengembangan karir fakultas/universitas","Menghubungi kantor kemahasiswaan/hubungan alumni","Membangun jejaring (network) sejak masih kuliah","Melalui relasi (misalnya dosen, orang tua, saudara, teman, dll.)","Membangun bisnis sendiri","Melalui penempatan kerja atau magang","Bekerja di tempat yang sama dengan tempat kerja semasa kuliah","Lainnya"]', '44'],

            ['f416', 'Tuliskan cara mencari pekerjaan lainnya', 'text', 'text',
             'Isi jika memilih "Lainnya" pada pertanyaan sebelumnya.', '[]', '45'],

            ['f6', 'Berapa perusahaan/instansi/institusi yang sudah Anda lamar sebelum memperoleh pekerjaan pertama?', 'text', 'number',
             'Hitung total perusahaan/instansi yang pernah Anda lamar sebelum mendapat pekerjaan pertama.', '[]', '46'],

            ['f7', 'Berapa banyak perusahaan/instansi/institusi yang merespons lamaran Anda?', 'text', 'number',
             'Hitung berapa perusahaan yang memberikan respons atas lamaran Anda.', '[]', '47'],

            ['f7a', 'Berapa banyak perusahaan/instansi/institusi yang mengundang Anda untuk wawancara?', 'text', 'number',
             'Hitung berapa perusahaan yang mengundang Anda untuk wawancara kerja.', '[]', '48'],

            ['f1001', 'Apakah Anda aktif mencari pekerjaan dalam 4 minggu terakhir?', 'single', 'text',
             'Pilih yang paling sesuai dengan kondisi Anda dalam 4 minggu terakhir ini.',
             '["Tidak","Tidak, tapi saya sedang menunggu hasil lamaran kerja","Ya, saya akan mulai bekerja dalam 2 minggu ke depan","Ya, tapi saya belum pasti akan bekerja dalam 2 minggu ke depan","Lainnya"]', '49'],

            ['f1002', 'Tuliskan aktivitas mencari pekerjaan lainnya dalam 4 minggu terakhir', 'text', 'text',
             'Isi jika memilih "Lainnya" pada pertanyaan sebelumnya.', '[]', '50'],

            ['f1601-f1614', 'Jika pekerjaan Anda saat ini tidak sesuai dengan pendidikan Anda, mengapa Anda mengambilnya? Jawaban bisa lebih dari satu', 'multiple', 'text',
             'Jika pekerjaan Anda saat ini tidak sesuai dengan bidang studi, pilih semua alasan yang berlaku.',
             '["Pekerjaan saya sekarang sudah sesuai dengan pendidikan saya","Saya belum mendapatkan pekerjaan yang lebih sesuai","Di pekerjaan ini saya memperoleh prospek karir yang baik","Saya lebih suka bekerja di area pekerjaan yang tidak ada hubungannya dengan pendidikan saya","Saya dipromosikan ke posisi yang kurang berhubungan dengan pendidikan saya dibanding posisi sebelumnya","Saya dapat memperoleh pendapatan yang lebih tinggi di pekerjaan ini","Pekerjaan saya saat ini lebih aman/terjamin/secure","Pekerjaan saya saat ini lebih menarik","Pekerjaan saya saat ini lebih memungkinkan saya mengambil pekerjaan tambahan/jadwal fleksibel","Pekerjaan saya saat ini lokasinya lebih dekat dari rumah saya","Pekerjaan saya saat ini dapat lebih menjamin kebutuhan keluarga saya","Pada awal meniti karir ini, saya harus menerima pekerjaan yang tidak berhubungan dengan pendidikan saya","Lainnya"]', '51'],

            ['f1614', 'Tuliskan alasan lainnya mengambil pekerjaan yang tidak sesuai pendidikan', 'text', 'text',
             'Isi jika memilih "Lainnya" pada pertanyaan sebelumnya.', '[]', '52'],
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
            ['4. Tipe Data: text, number, date, year (hanya berlaku jika Tipe = text)'],
            ['5. Keterangan: Deskripsi atau petunjuk untuk alumni'],
            ['6. Options (JSON): Format array JSON untuk pilihan ganda/single (contoh: ["Opsi 1","Opsi 2"])'],
            ['7. Urutan: Nomor urutan pertanyaan dalam survey'],
            [''],
            ['KETERANGAN TIPE DATA (kolom 4):'],
            ['- text   : Input teks bebas (default)'],
            ['- number : Input angka (contoh: pendapatan, jumlah bulan)'],
            ['- date   : Input tanggal (contoh: tanggal masuk studi lanjut)'],
            ['- year   : Input tahun'],
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
        $fileName = 'Template_Pertanyaan_' . date('d-m-Y_H:i') . '.xlsx';

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
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ], [
            'file.required' => 'File wajib diunggah.',
            'file.mimes'    => 'File harus berformat xlsx atau xls.',
            'file.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

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
                    $kode_soal    = trim($row[0] ?? '');
                    $pertanyaan   = trim($row[1] ?? '');
                    $tipe         = trim($row[2] ?? '');
                    $tipe_data    = trim($row[3] ?? 'text'); // kolom baru
                    $keterangan   = trim($row[4] ?? '');
                    $options_json = trim($row[5] ?? '[]');
                    $urutan       = intval($row[6] ?? 1);

                    // Validasi tipe_data
                    if (!in_array($tipe_data, ['text', 'number', 'date', 'year'])) {
                        $tipe_data = 'text';
                    }

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
                        $existingUrutan = Question::where('urutan', $urutan)->first();
                        if ($existingUrutan && (!$kode_soal || $existingUrutan->kode_soal !== $kode_soal)) {
                            $urutan = $maxUrutan + 1;
                        }
                    }

                    // Create or update question
                    $question = Question::updateOrCreate(
                        ['kode_soal' => $kode_soal ?: null],
                        [
                            'pertanyaan' => $pertanyaan,
                            'type'       => $tipe,
                            'tipe_data'  => $tipe_data,
                            'hint'       => $keterangan,
                            'urutan'     => $urutan,
                        ]
                    );

                    // Delete existing options and create new ones
                    $question->options()->delete();
                    foreach ($options as $idx => $option) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'label'       => $option,
                            'value'       => $idx + 1,
                            'urutan'      => $idx + 1,
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
