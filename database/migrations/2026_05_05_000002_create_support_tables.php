<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Provinsi
        Schema::create('provinsi', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->timestamps();
        });

        // Tabel Kota/Kabupaten
        Schema::create('kota_kabupaten', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provinsi_id')->constrained('provinsi')->cascadeOnDelete();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->timestamps();
        });

        // Tabel Jenis Instansi (untuk master data pekerjaan)
        Schema::create('jenis_instansi', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_instansi');
        Schema::dropIfExists('kota_kabupaten');
        Schema::dropIfExists('provinsi');
    }
};
