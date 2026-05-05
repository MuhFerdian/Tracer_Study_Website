<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Expand kolom alumni sesuai panduan form PDF
        Schema::table('alumni', function (Blueprint $table) {
            // Identitas tambahan
            if (!Schema::hasColumn('alumni', 'nik')) {
                $table->string('nik')->nullable()->after('email');
            }
            if (!Schema::hasColumn('alumni', 'npwp')) {
                $table->string('npwp')->nullable()->after('nik');
            }
            if (!Schema::hasColumn('alumni', 'kode_pt')) {
                $table->string('kode_pt')->nullable()->after('nim');
            }
            if (!Schema::hasColumn('alumni', 'kode_prodi')) {
                $table->string('kode_prodi')->nullable()->after('prodi');
            }

            // Pekerjaan detail
            if (!Schema::hasColumn('alumni', 'salary')) {
                $table->bigInteger('salary')->nullable()->comment('Rata-rata pendapatan per bulan (take home pay)')->after('posisi');
            }
            if (!Schema::hasColumn('alumni', 'provinsi_kerja')) {
                $table->string('provinsi_kerja')->nullable()->after('salary');
            }
            if (!Schema::hasColumn('alumni', 'kota_kerja')) {
                $table->string('kota_kerja')->nullable()->after('provinsi_kerja');
            }
            if (!Schema::hasColumn('alumni', 'jenis_instansi')) {
                $table->enum('jenis_instansi', [
                    'pemerintah',
                    'bumn_bumd',
                    'multilateral',
                    'ngo',
                    'swasta',
                    'wiraswasta',
                    'lainnya'
                ])->nullable()->after('kota_kerja');
            }
            if (!Schema::hasColumn('alumni', 'bulan_dapat_kerja')) {
                $table->integer('bulan_dapat_kerja')->nullable()->comment('Bulan setelah lulus mendapat kerja pertama')->after('jenis_instansi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('alumni', function (Blueprint $table) {
            $columns = ['nik', 'npwp', 'kode_pt', 'kode_prodi', 'salary', 'provinsi_kerja', 'kota_kerja', 'jenis_instansi', 'bulan_dapat_kerja'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('alumni', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
