<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing questions
        DB::table('question_options')->delete();
        DB::table('questions')->delete();

        // Pertanyaan dan options berdasarkan PDF panduan form
        $questionsData = [
            // Q1
            [
                'kode_soal' => 'f8',
                'pertanyaan' => 'Jelaskan status Anda saat ini?',
                'type' => 'single',
                'is_required' => true,
                'urutan' => 1,
                'options' => [
                    ['label' => 'Bekerja (full time / part time)', 'value' => '1'],
                    ['label' => 'Belum memungkinkan bekerja', 'value' => '2'],
                    ['label' => 'Wiraswasta', 'value' => '3'],
                    ['label' => 'Melanjutkan Pendidikan', 'value' => '4'],
                    ['label' => 'Tidak kerja tetapi sedang mencari kerja', 'value' => '5'],
                ]
            ],
            // Q2
            [
                'kode_soal' => 'f502',
                'pertanyaan' => 'Dalam berapa bulan Anda mendapatkan pekerjaan pertama? (Jika Memilih Bekerja)',
                'type' => 'text',
                'is_required' => false,
                'urutan' => 2,
                'options' => []
            ],
            // Q3
            [
                'kode_soal' => 'f502b',
                'pertanyaan' => 'Dalam berapa bulan setelah lulus anda memulai wiraswasta? (Jika Memilih Wiraswasta)',
                'type' => 'text',
                'is_required' => false,
                'urutan' => 3,
                'options' => []
            ],
            // Q4
            [
                'kode_soal' => 'f505',
                'pertanyaan' => 'Berapa rata-rata pendapatan Anda per bulan? (take home pay)?',
                'type' => 'text',
                'is_required' => false,
                'urutan' => 4,
                'options' => []
            ],
            // Q5
            [
                'kode_soal' => 'f5a1',
                'pertanyaan' => 'Dimana lokasi tempat Anda bekerja? (Provinsi)',
                'type' => 'single',
                'is_required' => false,
                'urutan' => 5,
                'options' => []
            ],
            // Q6
            [
                'kode_soal' => 'f5a2',
                'pertanyaan' => 'Dimana lokasi tempat Anda bekerja? (Kota/Kabupaten)',
                'type' => 'single',
                'is_required' => false,
                'urutan' => 6,
                'options' => []
            ],
            // Q7
            [
                'kode_soal' => 'f1101',
                'pertanyaan' => 'Apa jenis perusahaan/intansi/institusi tempat anda bekerja sekarang?',
                'type' => 'single',
                'is_required' => false,
                'urutan' => 7,
                'options' => [
                    ['label' => 'Intansi pemerintah', 'value' => '1'],
                    ['label' => 'BUMN/BUMD', 'value' => '6'],
                    ['label' => 'Institusi/Organisasi Multilateral', 'value' => '7'],
                    ['label' => 'Organisasi non-profit/Lembaga Swadaya Masyarakat', 'value' => '2'],
                    ['label' => 'Perusahaan swasta', 'value' => '3'],
                    ['label' => 'Wiraswasta/perusahaan sendiri', 'value' => '4'],
                    ['label' => 'Lainnya, tuliskan', 'value' => '5'],
                ]
            ],
            // Q8
            [
                'kode_soal' => 'f5b',
                'pertanyaan' => 'Apa nama perusahaan/kantor tempat Anda bekerja?',
                'type' => 'text',
                'is_required' => false,
                'urutan' => 8,
                'options' => []
            ],
            // Q9
            [
                'kode_soal' => 'f5c',
                'pertanyaan' => 'Bila berwiraswasta, apa posisi/jabatan Anda saat ini?',
                'type' => 'single',
                'is_required' => false,
                'urutan' => 9,
                'options' => []
            ],
            // Q10
            [
                'kode_soal' => 'f5d',
                'pertanyaan' => 'Apa tingkat tempat kerja Anda?',
                'type' => 'single',
                'is_required' => false,
                'urutan' => 10,
                'options' => []
            ],
            // Q11 - Studi Lanjut
            [
                'kode_soal' => 'f18a',
                'pertanyaan' => 'Pertanyaan studi lanjut - Sumber biaya',
                'type' => 'single',
                'is_required' => false,
                'urutan' => 11,
                'options' => []
            ],
            // Q12
            [
                'kode_soal' => 'f18b',
                'pertanyaan' => 'Pertanyaan studi lanjut - Perguruan Tinggi',
                'type' => 'text',
                'is_required' => false,
                'urutan' => 12,
                'options' => []
            ],
            // Q13
            [
                'kode_soal' => 'f18c',
                'pertanyaan' => 'Pertanyaan studi lanjut - Program Studi',
                'type' => 'text',
                'is_required' => false,
                'urutan' => 13,
                'options' => []
            ],
            // Q14
            [
                'kode_soal' => 'f18d',
                'pertanyaan' => 'Pertanyaan studi lanjut - Tanggal Masuk',
                'type' => 'text',
                'is_required' => false,
                'urutan' => 14,
                'options' => []
            ],
            // Q15
            [
                'kode_soal' => 'f1201',
                'pertanyaan' => 'Sebutkan sumberdana dalam pembiayaan kuliah? (bukan ketika Studi Lanjut)',
                'type' => 'single',
                'is_required' => true,
                'urutan' => 15,
                'options' => [
                    ['label' => 'Biaya Sendiri/Keluarga', 'value' => '1'],
                    ['label' => 'Beasiswa ADIK', 'value' => '2'],
                    ['label' => 'Beasiswa BIDIKMISI', 'value' => '3'],
                    ['label' => 'Beasiswa PPA', 'value' => '4'],
                    ['label' => 'Beasiswa AFIRMASI', 'value' => '5'],
                    ['label' => 'Beasiswa Perusahaan/Swasta', 'value' => '6'],
                    ['label' => 'Lainnya, tuliskan', 'value' => '7'],
                ]
            ],
            // Q16
            [
                'kode_soal' => 'f14',
                'pertanyaan' => 'Seberapa erat hubungan bidang studi dengan pekerjaan Anda?',
                'type' => 'single',
                'is_required' => true,
                'urutan' => 16,
                'options' => [
                    ['label' => 'Sangat Erat', 'value' => '1'],
                    ['label' => 'Erat', 'value' => '2'],
                    ['label' => 'Cukup Erat', 'value' => '3'],
                    ['label' => 'Kurang Erat', 'value' => '4'],
                    ['label' => 'Tidak Sama Sekali', 'value' => '5'],
                ]
            ],
            // Q17
            [
                'kode_soal' => 'f15',
                'pertanyaan' => 'Tingkat pendidikan apa yang paling tepat/sesuai untuk pekerjaan anda saat ini?',
                'type' => 'single',
                'is_required' => true,
                'urutan' => 17,
                'options' => [
                    ['label' => 'Setingkat Lebih Tinggi', 'value' => '1'],
                    ['label' => 'Tingkat yang Sama', 'value' => '2'],
                    ['label' => 'Setingkat Lebih Rendah', 'value' => '3'],
                    ['label' => 'Tidak Perlu Pendidikan Tinggi', 'value' => '4'],
                ]
            ],
            // Q18 - Kompetensi (matrix) - akan dibagi menjadi multiple questions
            [
                'kode_soal' => 'f1761',
                'pertanyaan' => 'Pada saat lulus, pada tingkat mana kompetensi ETIKA Anda kuasai?',
                'type' => 'single',
                'is_required' => true,
                'urutan' => 18,
                'options' => [
                    ['label' => 'Sangat Rendah', 'value' => '1'],
                    ['label' => 'Rendah', 'value' => '2'],
                    ['label' => 'Sedang', 'value' => '3'],
                    ['label' => 'Tinggi', 'value' => '4'],
                    ['label' => 'Sangat Tinggi', 'value' => '5'],
                ]
            ],
            // Q19
            [
                'kode_soal' => 'f1762',
                'pertanyaan' => 'Pada saat ini, pada tingkat mana kompetensi ETIKA diperlukan dalam pekerjaan?',
                'type' => 'single',
                'is_required' => true,
                'urutan' => 19,
                'options' => [
                    ['label' => 'Sangat Rendah', 'value' => '1'],
                    ['label' => 'Rendah', 'value' => '2'],
                    ['label' => 'Sedang', 'value' => '3'],
                    ['label' => 'Tinggi', 'value' => '4'],
                    ['label' => 'Sangat Tinggi', 'value' => '5'],
                ]
            ],
            // Q20
            [
                'kode_soal' => 'f301',
                'pertanyaan' => 'Kapan anda mulai mencari pekerjaan? (Mohon pekerjaan sambilan tidak dimasukkan)',
                'type' => 'single',
                'is_required' => false,
                'urutan' => 20,
                'options' => [
                    ['label' => 'Kira-kira berapa bulan sebelum lulus', 'value' => '1'],
                    ['label' => 'Kira-kira berapa bulan sesudah lulus', 'value' => '2'],
                    ['label' => 'Saya tidak mencari kerja', 'value' => '3'],
                ]
            ],
            // Q21
            [
                'kode_soal' => 'f416',
                'pertanyaan' => 'Bagaimana anda mencari pekerjaan tersebut? (Jawaban bisa lebih dari satu)',
                'type' => 'multiple',
                'is_required' => false,
                'urutan' => 21,
                'options' => [
                    ['label' => 'Melalui iklan di koran/majalah, brosur', 'value' => '1'],
                    ['label' => 'Melamar ke perusahaan tanpa mengetahui lowongan yang ada', 'value' => '2'],
                    ['label' => 'Pergi ke bursa/pameran kerja', 'value' => '3'],
                    ['label' => 'Mencari lewat internet/iklan online/milis', 'value' => '4'],
                    ['label' => 'Dihubungi oleh perusahaan', 'value' => '5'],
                    ['label' => 'Menghubungi Kemenakertrans', 'value' => '6'],
                    ['label' => 'Menghubungi agen tenaga kerja komersial/swasta', 'value' => '7'],
                    ['label' => 'Memeroleh informasi dari pusat/kantor pengembangan karir', 'value' => '8'],
                    ['label' => 'Menghubungi kantor kemahasiswaan/hubungan alumni', 'value' => '9'],
                    ['label' => 'Membangun jejaring (network) sejak masih kuliah', 'value' => '10'],
                    ['label' => 'Melalui relasi (dosen, orang tua, saudara, teman, dll.)', 'value' => '11'],
                    ['label' => 'Membangun bisnis sendiri', 'value' => '12'],
                    ['label' => 'Melalui penempatan kerja atau magang', 'value' => '13'],
                    ['label' => 'Bekerja di tempat yang sama dengan tempat kerja semasa kuliah', 'value' => '14'],
                    ['label' => 'Lainnya', 'value' => '15'],
                ]
            ],
        ];

        // Insert questions dan options
        foreach ($questionsData as $q) {
            $options = $q['options'];
            unset($q['options']);

            $question = DB::table('questions')->insertGetId($q);

            // Insert options
            if (!empty($options)) {
                foreach ($options as $i => $opt) {
                    DB::table('question_options')->insert([
                        'question_id' => $question,
                        'label' => $opt['label'],
                        'value' => $opt['value'],
                        'urutan' => $i + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
