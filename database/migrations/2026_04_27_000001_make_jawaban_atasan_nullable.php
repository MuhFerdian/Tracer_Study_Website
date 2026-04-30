<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jadikan kolom atasan_id di tabel jawaban menjadi nullable.
     * Survei sekarang tidak lagi membutuhkan atasan_id (role Atasan sudah dihapus).
     * Kolom dipertahankan untuk kompatibilitas data historis.
     */
    public function up(): void
    {
        Schema::table('jawaban', function (Blueprint $table) {
            $table->unsignedBigInteger('atasan_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jawaban', function (Blueprint $table) {
            $table->unsignedBigInteger('atasan_id')->nullable(false)->change();
        });
    }
};
