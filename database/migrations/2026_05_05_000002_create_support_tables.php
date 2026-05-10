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
        // Catatan: jenis_instansi dihapus dari master table.
        // Data jenis instansi diambil langsung dari jawaban questionnaire
        // (question f1101 → answer_details → question_options.label)
    }

    public function down(): void
    {
        Schema::dropIfExists('kota_kabupaten');
        Schema::dropIfExists('provinsi');
    }
};
