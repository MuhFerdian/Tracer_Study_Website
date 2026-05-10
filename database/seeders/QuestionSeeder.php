<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    private array $scaleOptions = [
        ['label' => 'Sangat Rendah', 'value' => '1'],
        ['label' => 'Rendah', 'value' => '2'],
        ['label' => 'Sedang', 'value' => '3'],
        ['label' => 'Tinggi', 'value' => '4'],
        ['label' => 'Sangat Tinggi', 'value' => '5'],
    ];

    public function run(): void
    {
        DB::table('answer_details')->delete();
        DB::table('answers')->delete();
        DB::table('question_details')->delete();
        DB::table('question_options')->delete();
        DB::table('questions')->delete();

        $questions = [
            $this->question('f8', 'Jelaskan status Anda saat ini?', 'single', true, [
                ['label' => 'Bekerja (full time / part time)', 'value' => '1'],
                ['label' => 'Belum memungkinkan bekerja', 'value' => '2'],
                ['label' => 'Wiraswasta', 'value' => '3'],
                ['label' => 'Melanjutkan Pendidikan', 'value' => '4'],
                ['label' => 'Tidak kerja tetapi sedang mencari kerja', 'value' => '5'],
            ]),
            $this->question('f502', 'Dalam berapa bulan Anda mendapatkan pekerjaan pertama? (Jika memilih bekerja)', 'text', false, [], 'number'),
            $this->question('f503', 'Dalam berapa bulan setelah lulus Anda memulai wiraswasta? (Jika memilih wiraswasta)', 'text', false, [], 'number'),
            $this->question('f505', 'Berapa rata-rata pendapatan Anda per bulan? (take home pay)', 'text', false, [], 'number'),
            $this->question('f5a1', 'Di mana lokasi tempat Anda bekerja? Provinsi', 'text', false),
            $this->question('f5a2', 'Di mana lokasi tempat Anda bekerja? Kota/Kabupaten', 'text', false),
            $this->question('f1101', 'Apa jenis perusahaan/instansi/institusi tempat Anda bekerja sekarang?', 'single', false, [
                ['label' => 'Instansi pemerintah', 'value' => '1'],
                ['label' => 'Organisasi non-profit/Lembaga Swadaya Masyarakat', 'value' => '2'],
                ['label' => 'Perusahaan swasta', 'value' => '3'],
                ['label' => 'Wiraswasta/perusahaan sendiri', 'value' => '4'],
                ['label' => 'Lainnya, tuliskan', 'value' => '5'],
                ['label' => 'BUMN/BUMD', 'value' => '6'],
                ['label' => 'Institusi/Organisasi Multilateral', 'value' => '7'],
            ]),
            $this->question('f1102', 'Tuliskan jenis perusahaan/instansi/institusi lainnya', 'text', false),
            $this->question('f5b', 'Apa nama perusahaan/kantor tempat Anda bekerja?', 'text', false),
            $this->question('f5c', 'Bila berwiraswasta, apa posisi/jabatan Anda saat ini?', 'single', false, [
                ['label' => 'Founder', 'value' => '1'],
                ['label' => 'Co-Founder', 'value' => '2'],
                ['label' => 'Staff', 'value' => '3'],
                ['label' => 'Freelance/Kerja lepas', 'value' => '4'],
            ]),
            $this->question('f5d', 'Apa tingkat tempat kerja Anda?', 'single', false, [
                ['label' => 'Lokal/wilayah/wiraswasta tidak berbadan hukum', 'value' => '1'],
                ['label' => 'Nasional/wiraswasta berbadan hukum', 'value' => '2'],
                ['label' => 'Multinasional/internasional', 'value' => '3'],
            ]),
            $this->question('f18a', 'Pertanyaan studi lanjut: sumber biaya', 'single', false, [
                ['label' => 'Biaya sendiri', 'value' => '1'],
                ['label' => 'Beasiswa', 'value' => '2'],
            ]),
            $this->question('f18b', 'Pertanyaan studi lanjut: perguruan tinggi', 'text', false),
            $this->question('f18c', 'Pertanyaan studi lanjut: program studi', 'text', false),
            $this->question('f18d', 'Pertanyaan studi lanjut: tanggal masuk', 'text', false, [], 'date'),
            $this->question('f1201', 'Sebutkan sumber dana dalam pembiayaan kuliah? (bukan ketika studi lanjut)', 'single', true, [
                ['label' => 'Biaya Sendiri/Keluarga', 'value' => '1'],
                ['label' => 'Beasiswa ADIK', 'value' => '2'],
                ['label' => 'Beasiswa BIDIKMISI', 'value' => '3'],
                ['label' => 'Beasiswa PPA', 'value' => '4'],
                ['label' => 'Beasiswa AFIRMASI', 'value' => '5'],
                ['label' => 'Beasiswa Perusahaan/Swasta', 'value' => '6'],
                ['label' => 'Lainnya, tuliskan', 'value' => '7'],
            ]),
            $this->question('f1202', 'Tuliskan sumber dana lainnya dalam pembiayaan kuliah', 'text', false),
            $this->question('f14', 'Seberapa erat hubungan bidang studi dengan pekerjaan Anda?', 'single', true, [
                ['label' => 'Sangat Erat', 'value' => '1'],
                ['label' => 'Erat', 'value' => '2'],
                ['label' => 'Cukup Erat', 'value' => '3'],
                ['label' => 'Kurang Erat', 'value' => '4'],
                ['label' => 'Tidak Sama Sekali', 'value' => '5'],
            ]),
            $this->question('f15', 'Tingkat pendidikan apa yang paling tepat/sesuai untuk pekerjaan Anda saat ini?', 'single', true, [
                ['label' => 'Setingkat Lebih Tinggi', 'value' => '1'],
                ['label' => 'Tingkat yang Sama', 'value' => '2'],
                ['label' => 'Setingkat Lebih Rendah', 'value' => '3'],
                ['label' => 'Tidak Perlu Pendidikan Tinggi', 'value' => '4'],
            ]),
            $this->question('f1761', 'Pada saat lulus, pada tingkat mana kompetensi Etika Anda kuasai?', 'single', true, $this->scaleOptions),
            $this->question('f1762', 'Pada saat ini, pada tingkat mana kompetensi Etika diperlukan dalam pekerjaan?', 'single', true, $this->scaleOptions),
            $this->question('f1763', 'Pada saat lulus, pada tingkat mana kompetensi Keahlian berdasarkan bidang ilmu Anda kuasai?', 'single', true, $this->scaleOptions),
            $this->question('f1764', 'Pada saat ini, pada tingkat mana kompetensi Keahlian berdasarkan bidang ilmu diperlukan dalam pekerjaan?', 'single', true, $this->scaleOptions),
            $this->question('f1765', 'Pada saat lulus, pada tingkat mana kompetensi Bahasa Inggris Anda kuasai?', 'single', true, $this->scaleOptions),
            $this->question('f1766', 'Pada saat ini, pada tingkat mana kompetensi Bahasa Inggris diperlukan dalam pekerjaan?', 'single', true, $this->scaleOptions),
            $this->question('f1767', 'Pada saat lulus, pada tingkat mana kompetensi Penggunaan Teknologi Informasi Anda kuasai?', 'single', true, $this->scaleOptions),
            $this->question('f1768', 'Pada saat ini, pada tingkat mana kompetensi Penggunaan Teknologi Informasi diperlukan dalam pekerjaan?', 'single', true, $this->scaleOptions),
            $this->question('f1769', 'Pada saat lulus, pada tingkat mana kompetensi Komunikasi Anda kuasai?', 'single', true, $this->scaleOptions),
            $this->question('f1770', 'Pada saat ini, pada tingkat mana kompetensi Komunikasi diperlukan dalam pekerjaan?', 'single', true, $this->scaleOptions),
            $this->question('f1771', 'Pada saat lulus, pada tingkat mana kompetensi Kerja sama tim Anda kuasai?', 'single', true, $this->scaleOptions),
            $this->question('f1772', 'Pada saat ini, pada tingkat mana kompetensi Kerja sama tim diperlukan dalam pekerjaan?', 'single', true, $this->scaleOptions),
            $this->question('f1773', 'Pada saat lulus, pada tingkat mana kompetensi Pengembangan Anda kuasai?', 'single', true, $this->scaleOptions),
            $this->question('f1774', 'Pada saat ini, pada tingkat mana kompetensi Pengembangan diperlukan dalam pekerjaan?', 'single', true, $this->scaleOptions),
            $this->question('f21', 'Menurut Anda seberapa besar penekanan metode pembelajaran Perkuliahan dilaksanakan di program studi Anda?', 'single', true, $this->learningMethodOptions()),
            $this->question('f22', 'Menurut Anda seberapa besar penekanan metode pembelajaran Demonstrasi dilaksanakan di program studi Anda?', 'single', true, $this->learningMethodOptions()),
            $this->question('f23', 'Menurut Anda seberapa besar penekanan Partisipasi dalam proyek riset dilaksanakan di program studi Anda?', 'single', true, $this->learningMethodOptions()),
            $this->question('f24', 'Menurut Anda seberapa besar penekanan metode pembelajaran Magang dilaksanakan di program studi Anda?', 'single', true, $this->learningMethodOptions()),
            $this->question('f25', 'Menurut Anda seberapa besar penekanan metode pembelajaran Praktikum dilaksanakan di program studi Anda?', 'single', true, $this->learningMethodOptions()),
            $this->question('f26', 'Menurut Anda seberapa besar penekanan metode pembelajaran Kerja Lapangan dilaksanakan di program studi Anda?', 'single', true, $this->learningMethodOptions()),
            $this->question('f27', 'Menurut Anda seberapa besar penekanan metode pembelajaran Diskusi dilaksanakan di program studi Anda?', 'single', true, $this->learningMethodOptions()),
            $this->question('f301', 'Kapan Anda mulai mencari pekerjaan? Mohon pekerjaan sambilan tidak dimasukkan', 'single', false, [
                ['label' => 'Kira-kira bulan sebelum lulus', 'value' => '1'],
                ['label' => 'Kira-kira bulan sesudah lulus', 'value' => '2'],
                ['label' => 'Saya tidak mencari kerja', 'value' => '3'],
            ]),
            $this->question('f302', 'Berapa bulan sebelum lulus Anda mulai mencari pekerjaan?', 'text', false, [], 'number'),
            $this->question('f303', 'Berapa bulan sesudah lulus Anda mulai mencari pekerjaan?', 'text', false, [], 'number'),
            $this->question('f401-f416', 'Bagaimana Anda mencari pekerjaan tersebut? Jawaban bisa lebih dari satu', 'multiple', false, [
                ['label' => 'Melalui iklan di koran/majalah, brosur', 'value' => 'f401'],
                ['label' => 'Melamar ke perusahaan tanpa mengetahui lowongan yang ada', 'value' => 'f402'],
                ['label' => 'Pergi ke bursa/pameran kerja', 'value' => 'f403'],
                ['label' => 'Mencari lewat internet/iklan online/milis', 'value' => 'f404'],
                ['label' => 'Dihubungi oleh perusahaan', 'value' => 'f405'],
                ['label' => 'Menghubungi Kemenakertrans', 'value' => 'f406'],
                ['label' => 'Menghubungi agen tenaga kerja komersial/swasta', 'value' => 'f407'],
                ['label' => 'Memperoleh informasi dari pusat/kantor pengembangan karir fakultas/universitas', 'value' => 'f408'],
                ['label' => 'Menghubungi kantor kemahasiswaan/hubungan alumni', 'value' => 'f409'],
                ['label' => 'Membangun jejaring (network) sejak masih kuliah', 'value' => 'f410'],
                ['label' => 'Melalui relasi (misalnya dosen, orang tua, saudara, teman, dll.)', 'value' => 'f411'],
                ['label' => 'Membangun bisnis sendiri', 'value' => 'f412'],
                ['label' => 'Melalui penempatan kerja atau magang', 'value' => 'f413'],
                ['label' => 'Bekerja di tempat yang sama dengan tempat kerja semasa kuliah', 'value' => 'f414'],
                ['label' => 'Lainnya', 'value' => 'f415'],
            ]),
            $this->question('f416', 'Tuliskan cara mencari pekerjaan lainnya', 'text', false),
            $this->question('f6', 'Berapa perusahaan/instansi/institusi yang sudah Anda lamar sebelum memperoleh pekerjaan pertama?', 'text', false, [], 'number'),
            $this->question('f7', 'Berapa banyak perusahaan/instansi/institusi yang merespons lamaran Anda?', 'text', false, [], 'number'),
            $this->question('f7a', 'Berapa banyak perusahaan/instansi/institusi yang mengundang Anda untuk wawancara?', 'text', false, [], 'number'),
            $this->question('f1001', 'Apakah Anda aktif mencari pekerjaan dalam 4 minggu terakhir?', 'single', false, [
                ['label' => 'Tidak', 'value' => '1'],
                ['label' => 'Tidak, tapi saya sedang menunggu hasil lamaran kerja', 'value' => '2'],
                ['label' => 'Ya, saya akan mulai bekerja dalam 2 minggu ke depan', 'value' => '3'],
                ['label' => 'Ya, tapi saya belum pasti akan bekerja dalam 2 minggu ke depan', 'value' => '4'],
                ['label' => 'Lainnya', 'value' => '5'],
            ]),
            $this->question('f1002', 'Tuliskan aktivitas mencari pekerjaan lainnya dalam 4 minggu terakhir', 'text', false),
            $this->question('f1601-f1614', 'Jika pekerjaan Anda saat ini tidak sesuai dengan pendidikan Anda, mengapa Anda mengambilnya? Jawaban bisa lebih dari satu', 'multiple', false, [
                ['label' => 'Pekerjaan saya sekarang sudah sesuai dengan pendidikan saya', 'value' => 'f1601'],
                ['label' => 'Saya belum mendapatkan pekerjaan yang lebih sesuai', 'value' => 'f1602'],
                ['label' => 'Di pekerjaan ini saya memperoleh prospek karir yang baik', 'value' => 'f1603'],
                ['label' => 'Saya lebih suka bekerja di area pekerjaan yang tidak ada hubungannya dengan pendidikan saya', 'value' => 'f1604'],
                ['label' => 'Saya dipromosikan ke posisi yang kurang berhubungan dengan pendidikan saya dibanding posisi sebelumnya', 'value' => 'f1605'],
                ['label' => 'Saya dapat memperoleh pendapatan yang lebih tinggi di pekerjaan ini', 'value' => 'f1606'],
                ['label' => 'Pekerjaan saya saat ini lebih aman/terjamin/secure', 'value' => 'f1607'],
                ['label' => 'Pekerjaan saya saat ini lebih menarik', 'value' => 'f1608'],
                ['label' => 'Pekerjaan saya saat ini lebih memungkinkan saya mengambil pekerjaan tambahan/jadwal fleksibel', 'value' => 'f1609'],
                ['label' => 'Pekerjaan saya saat ini lokasinya lebih dekat dari rumah saya', 'value' => 'f1610'],
                ['label' => 'Pekerjaan saya saat ini dapat lebih menjamin kebutuhan keluarga saya', 'value' => 'f1611'],
                ['label' => 'Pada awal meniti karir ini, saya harus menerima pekerjaan yang tidak berhubungan dengan pendidikan saya', 'value' => 'f1612'],
                ['label' => 'Lainnya', 'value' => 'f1613'],
            ]),
            $this->question('f1614', 'Tuliskan alasan lainnya mengambil pekerjaan yang tidak sesuai pendidikan', 'text', false),
        ];

        foreach ($questions as $index => $questionData) {
            $options = $questionData['options'] ?? [];
            $details = $questionData['details'] ?? [];
            unset($questionData['options'], $questionData['details']);

            $questionData['urutan'] = $index + 1;
            $questionData['created_at'] = now();
            $questionData['updated_at'] = now();

            $questionId = DB::table('questions')->insertGetId($questionData);

            foreach ($options as $optionIndex => $option) {
                DB::table('question_options')->insert([
                    'question_id' => $questionId,
                    'label' => $option['label'],
                    'value' => $option['value'],
                    'urutan' => $optionIndex + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach ($details as $detailIndex => $detail) {
                DB::table('question_details')->insert([
                    'question_id' => $questionId,
                    'item_label' => $detail['label'],
                    'field_code_a' => $detail['field_code_a'] ?? null,
                    'field_code_b' => $detail['field_code_b'] ?? null,
                    'urutan' => $detailIndex + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function question(
        string $kodeSoal,
        string $pertanyaan,
        string $type,
        bool $required,
        array $options = [],
        string $tipeData = 'text'
    ): array {
        return [
            'kode_soal' => $kodeSoal,
            'pertanyaan' => $pertanyaan,
            'type' => $type,
            'tipe_data' => $tipeData,
            'is_required' => $required,
            'options' => $options,
        ];
    }

    private function learningMethodOptions(): array
    {
        return [
            ['label' => 'Sangat Besar', 'value' => '1'],
            ['label' => 'Besar', 'value' => '2'],
            ['label' => 'Cukup Besar', 'value' => '3'],
            ['label' => 'Kurang Besar', 'value' => '4'],
            ['label' => 'Tidak Sama Sekali', 'value' => '5'],
        ];
    }
}
