<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni', function (Blueprint $table) {
            $table->id();

            // Relasi ke users
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Data utama alumni
            $table->string('nim')->unique();
            $table->string('nama');
            $table->string('prodi')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->string('alamat')->nullable();
            // $table->string('tempat_lahir')->nullable();
            // $table->date('tanggal_lahir')->nullable();

            // Akademik
            $table->year('angkatan')->nullable();
            $table->year('tahun_lulus')->nullable();

            // Opsional (kalau mau dipakai nanti)
            $table->string('status_pekerjaan')->nullable(); // bekerja / belum
            $table->string('nama_instansi')->nullable();
            $table->string('posisi')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni');
    }
};