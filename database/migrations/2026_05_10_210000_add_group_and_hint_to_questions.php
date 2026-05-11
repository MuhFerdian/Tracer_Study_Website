<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom group_label dan hint
        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'group_label')) {
                $table->string('group_label')->nullable()->after('kode_soal')
                    ->comment('Label grup untuk mengelompokkan pertanyaan terkait, contoh: kompetensi, metode_pembelajaran');
            }
            if (!Schema::hasColumn('questions', 'hint')) {
                $table->text('hint')->nullable()->after('pertanyaan')
                    ->comment('Keterangan tambahan untuk membantu alumni memahami pertanyaan');
            }
        });

        // =============================================
        // Update hint untuk pertanyaan yang butuh keterangan
        // =============================================

        // f8 - status saat ini
        DB::table('questions')->where('kode_soal', 'f8')
            ->update(['hint' => 'Pilih satu status yang paling sesuai dengan kondisi Anda saat ini setelah lulus.']);

        // f502 - bulan dapat pekerjaan pertama (jika bekerja)
        DB::table('questions')->where('kode_soal', 'f502')
            ->update([
                'hint' => 'Hitung dari bulan kelulusan hingga Anda mulai bekerja pertama kali. Isi dengan angka, contoh: 3 (artinya 3 bulan setelah lulus).',
                'group_label' => 'status_bekerja',
            ]);

        // f503 - bulan mulai wiraswasta (jika wiraswasta)
        DB::table('questions')->where('kode_soal', 'f503')
            ->update([
                'hint' => 'Hitung dari bulan kelulusan hingga Anda memulai usaha/wiraswasta. Isi dengan angka.',
                'group_label' => 'status_wiraswasta',
            ]);

        // f505 - pendapatan per bulan
        DB::table('questions')->where('kode_soal', 'f505')
            ->update(['hint' => 'Isi dengan total pendapatan bersih yang Anda terima per bulan (take home pay) dalam Rupiah. Contoh: 5000000']);

        // f5a1 - provinsi
        DB::table('questions')->where('kode_soal', 'f5a1')
            ->update(['hint' => 'Tuliskan nama provinsi tempat Anda bekerja saat ini. Contoh: Jawa Timur']);

        // f5a2 - kota/kabupaten
        DB::table('questions')->where('kode_soal', 'f5a2')
            ->update(['hint' => 'Tuliskan nama kota atau kabupaten tempat Anda bekerja. Contoh: Kota Malang']);

        // f1101 - jenis perusahaan
        DB::table('questions')->where('kode_soal', 'f1101')
            ->update(['hint' => 'Pilih jenis instansi/perusahaan tempat Anda bekerja saat ini.']);

        // f1102 - jenis lainnya
        DB::table('questions')->where('kode_soal', 'f1102')
            ->update(['hint' => 'Tuliskan jenis instansi/perusahaan Anda jika memilih "Lainnya" pada pertanyaan sebelumnya.']);

        // f5b - nama perusahaan
        DB::table('questions')->where('kode_soal', 'f5b')
            ->update(['hint' => 'Tuliskan nama lengkap perusahaan, instansi, atau organisasi tempat Anda bekerja.']);

        // f5c - posisi wiraswasta
        DB::table('questions')->where('kode_soal', 'f5c')
            ->update(['hint' => 'Pilih posisi/jabatan Anda dalam usaha yang Anda jalankan.']);

        // f5d - tingkat tempat kerja
        DB::table('questions')->where('kode_soal', 'f5d')
            ->update(['hint' => 'Pilih skala operasional tempat Anda bekerja, apakah hanya beroperasi di tingkat lokal, nasional, atau internasional.']);

        // f18a - sumber biaya studi lanjut
        DB::table('questions')->where('kode_soal', 'f18a')
            ->update([
                'hint' => 'Jawab pertanyaan ini jika Anda memilih "Melanjutkan Pendidikan" pada pertanyaan status.',
                'group_label' => 'studi_lanjut',
            ]);

        // f18b - perguruan tinggi studi lanjut
        DB::table('questions')->where('kode_soal', 'f18b')
            ->update([
                'hint' => 'Tuliskan nama perguruan tinggi tempat Anda melanjutkan studi.',
                'group_label' => 'studi_lanjut',
            ]);

        // f18c - program studi lanjut
        DB::table('questions')->where('kode_soal', 'f18c')
            ->update([
                'hint' => 'Tuliskan nama program studi yang Anda ambil.',
                'group_label' => 'studi_lanjut',
            ]);

        // f18d - tanggal masuk studi lanjut
        DB::table('questions')->where('kode_soal', 'f18d')
            ->update([
                'hint' => 'Pilih tanggal Anda mulai masuk/terdaftar di perguruan tinggi tersebut.',
                'group_label' => 'studi_lanjut',
            ]);

        // f1201 - sumber dana kuliah
        DB::table('questions')->where('kode_soal', 'f1201')
            ->update(['hint' => 'Pilih sumber dana utama yang membiayai kuliah Anda (bukan untuk studi lanjut saat ini).']);

        // f1202 - sumber dana lainnya
        DB::table('questions')->where('kode_soal', 'f1202')
            ->update(['hint' => 'Tuliskan sumber dana lainnya jika memilih "Lainnya" pada pertanyaan sebelumnya.']);

        // f14 - kesesuaian bidang studi
        DB::table('questions')->where('kode_soal', 'f14')
            ->update(['hint' => 'Nilai seberapa erat keterkaitan antara bidang ilmu yang Anda pelajari dengan pekerjaan Anda saat ini.']);

        // f15 - tingkat pendidikan sesuai pekerjaan
        DB::table('questions')->where('kode_soal', 'f15')
            ->update(['hint' => 'Menurut Anda, tingkat pendidikan apa yang paling tepat untuk posisi pekerjaan Anda saat ini?']);

        // f1761-f1774 - kompetensi saat lulus (A) dan saat ini (B)
        $kompetensiHints = [
            'f1761' => ['hint' => 'Nilai tingkat penguasaan kompetensi Etika Anda pada saat lulus kuliah. (1=Sangat Rendah, 5=Sangat Tinggi)', 'group_label' => 'kompetensi_lulus'],
            'f1762' => ['hint' => 'Nilai seberapa penting kompetensi Etika dalam pekerjaan Anda saat ini. (1=Sangat Rendah, 5=Sangat Tinggi)', 'group_label' => 'kompetensi_kerja'],
            'f1763' => ['hint' => 'Nilai tingkat penguasaan Keahlian bidang ilmu Anda pada saat lulus. (1=Sangat Rendah, 5=Sangat Tinggi)', 'group_label' => 'kompetensi_lulus'],
            'f1764' => ['hint' => 'Nilai seberapa penting Keahlian bidang ilmu dalam pekerjaan Anda. (1=Sangat Rendah, 5=Sangat Tinggi)', 'group_label' => 'kompetensi_kerja'],
            'f1765' => ['hint' => 'Nilai tingkat penguasaan Bahasa Inggris Anda pada saat lulus. (1=Sangat Rendah, 5=Sangat Tinggi)', 'group_label' => 'kompetensi_lulus'],
            'f1766' => ['hint' => 'Nilai seberapa penting Bahasa Inggris dalam pekerjaan Anda. (1=Sangat Rendah, 5=Sangat Tinggi)', 'group_label' => 'kompetensi_kerja'],
            'f1767' => ['hint' => 'Nilai tingkat penguasaan Teknologi Informasi Anda pada saat lulus. (1=Sangat Rendah, 5=Sangat Tinggi)', 'group_label' => 'kompetensi_lulus'],
            'f1768' => ['hint' => 'Nilai seberapa penting Teknologi Informasi dalam pekerjaan Anda. (1=Sangat Rendah, 5=Sangat Tinggi)', 'group_label' => 'kompetensi_kerja'],
            'f1769' => ['hint' => 'Nilai tingkat kemampuan Komunikasi Anda pada saat lulus. (1=Sangat Rendah, 5=Sangat Tinggi)', 'group_label' => 'kompetensi_lulus'],
            'f1770' => ['hint' => 'Nilai seberapa penting kemampuan Komunikasi dalam pekerjaan Anda. (1=Sangat Rendah, 5=Sangat Tinggi)', 'group_label' => 'kompetensi_kerja'],
            'f1771' => ['hint' => 'Nilai tingkat kemampuan Kerja sama tim Anda pada saat lulus. (1=Sangat Rendah, 5=Sangat Tinggi)', 'group_label' => 'kompetensi_lulus'],
            'f1772' => ['hint' => 'Nilai seberapa penting Kerja sama tim dalam pekerjaan Anda. (1=Sangat Rendah, 5=Sangat Tinggi)', 'group_label' => 'kompetensi_kerja'],
            'f1773' => ['hint' => 'Nilai tingkat kemampuan Pengembangan diri Anda pada saat lulus. (1=Sangat Rendah, 5=Sangat Tinggi)', 'group_label' => 'kompetensi_lulus'],
            'f1774' => ['hint' => 'Nilai seberapa penting Pengembangan diri dalam pekerjaan Anda. (1=Sangat Rendah, 5=Sangat Tinggi)', 'group_label' => 'kompetensi_kerja'],
        ];

        foreach ($kompetensiHints as $kode => $data) {
            DB::table('questions')->where('kode_soal', $kode)->update($data);
        }

        // f21-f27 - metode pembelajaran
        $metodePembelajaran = [
            'f21' => 'Nilai seberapa besar metode Perkuliahan (tatap muka di kelas) ditekankan dalam program studi Anda.',
            'f22' => 'Nilai seberapa besar metode Demonstrasi (peragaan langsung oleh dosen/instruktur) ditekankan.',
            'f23' => 'Nilai seberapa besar keterlibatan dalam Proyek Riset/Penelitian ditekankan dalam program studi Anda.',
            'f24' => 'Nilai seberapa besar program Magang (kerja praktik di perusahaan/instansi) ditekankan.',
            'f25' => 'Nilai seberapa besar kegiatan Praktikum (di laboratorium atau studio) ditekankan.',
            'f26' => 'Nilai seberapa besar kegiatan Kerja Lapangan (survei, observasi, dll.) ditekankan.',
            'f27' => 'Nilai seberapa besar metode Diskusi (kelas, kelompok, seminar) ditekankan dalam program studi Anda.',
        ];

        foreach ($metodePembelajaran as $kode => $hint) {
            DB::table('questions')->where('kode_soal', $kode)->update([
                'hint' => $hint,
                'group_label' => 'metode_pembelajaran',
            ]);
        }

        // f301 - kapan mulai cari kerja
        DB::table('questions')->where('kode_soal', 'f301')
            ->update(['hint' => 'Pilih kapan Anda mulai aktif mencari pekerjaan tetap (bukan pekerjaan sambilan/part-time).']);

        // f302 - berapa bulan sebelum lulus
        DB::table('questions')->where('kode_soal', 'f302')
            ->update([
                'hint' => 'Isi dengan jumlah bulan sebelum kelulusan Anda mulai mencari kerja. Contoh: 3',
                'group_label' => 'waktu_cari_kerja_sebelum',
            ]);

        // f303 - berapa bulan sesudah lulus
        DB::table('questions')->where('kode_soal', 'f303')
            ->update([
                'hint' => 'Isi dengan jumlah bulan setelah kelulusan Anda mulai mencari kerja. Contoh: 2',
                'group_label' => 'waktu_cari_kerja_sesudah',
            ]);

        // f401-f416 - cara mencari kerja
        DB::table('questions')->where('kode_soal', 'f401-f416')
            ->update(['hint' => 'Pilih semua cara yang pernah Anda gunakan untuk mencari pekerjaan pertama. Boleh memilih lebih dari satu.']);

        // f416 - cara lainnya
        DB::table('questions')->where('kode_soal', 'f416')
            ->update(['hint' => 'Tuliskan cara lain yang Anda gunakan jika memilih "Lainnya" pada pertanyaan sebelumnya.']);

        // f6 - jumlah lamaran
        DB::table('questions')->where('kode_soal', 'f6')
            ->update(['hint' => 'Hitung total perusahaan/instansi yang pernah Anda lamar (via surat, email, atau platform online) sebelum mendapat pekerjaan pertama.']);

        // f7 - yang merespons
        DB::table('questions')->where('kode_soal', 'f7')
            ->update(['hint' => 'Hitung berapa perusahaan yang memberikan respons (balasan email, telepon, atau undangan tes) atas lamaran Anda.']);

        // f7a - yang mengundang wawancara
        DB::table('questions')->where('kode_soal', 'f7a')
            ->update(['hint' => 'Hitung berapa perusahaan yang mengundang Anda untuk wawancara kerja.']);

        // f1001 - aktif cari kerja 4 minggu terakhir
        DB::table('questions')->where('kode_soal', 'f1001')
            ->update(['hint' => 'Pilih yang paling sesuai dengan kondisi Anda dalam 4 minggu terakhir ini.']);

        // f1002 - aktivitas lainnya
        DB::table('questions')->where('kode_soal', 'f1002')
            ->update(['hint' => 'Tuliskan aktivitas pencarian kerja lainnya jika memilih "Lainnya" pada pertanyaan sebelumnya.']);

        // f1601-f1614 - alasan pekerjaan tidak sesuai
        DB::table('questions')->where('kode_soal', 'f1601-f1614')
            ->update(['hint' => 'Jika pekerjaan Anda saat ini tidak sesuai dengan bidang studi, pilih semua alasan yang berlaku. Jika sudah sesuai, pilih opsi pertama.']);

        // f1614 - alasan lainnya
        DB::table('questions')->where('kode_soal', 'f1614')
            ->update(['hint' => 'Tuliskan alasan lain jika memilih "Lainnya" pada pertanyaan sebelumnya.']);
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['group_label', 'hint']);
        });
    }
};
