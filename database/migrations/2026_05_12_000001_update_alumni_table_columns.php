<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumni', function (Blueprint $table) {
            // Hapus kolom yang tidak dipakai
            $table->dropColumn(['status_pekerjaan', 'nama_instansi', 'posisi']);

            // Tambah kolom baru
            $table->string('tempat_lahir')->nullable()->after('alamat');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
        });
    }

    public function down(): void
    {
        Schema::table('alumni', function (Blueprint $table) {
            // Kembalikan kolom yang dihapus
            $table->string('status_pekerjaan')->nullable();
            $table->string('nama_instansi')->nullable();
            $table->string('posisi')->nullable();

            // Hapus kolom yang ditambah
            $table->dropColumn(['tempat_lahir', 'tanggal_lahir']);
        });
    }
};
