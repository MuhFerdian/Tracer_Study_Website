<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run migrations
     */
    public function up(): void
    {
        Schema::create('lowongan_pekerjaan', function (Blueprint $table) {

            $table->id();

            // data lowongan
            $table->string('posisi');

            $table->string('nama_perusahaan');

            $table->string('lokasi')->nullable();

            $table->string('gaji')->nullable();

            $table->text('deskripsi')->nullable();

            $table->date('batas_lamaran')->nullable();

            $table->string('kontak')->nullable();

            $table->string('link_lamaran')->nullable();

            // pembuat lowongan
            $table->unsignedBigInteger('dibuat_oleh')->nullable();

            // role pembuat
            $table->enum('role', [
                'admin',
                'dosen',
                'alumni'
            ])->default('alumni');

            // status lowongan
            $table->boolean('aktif')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse migrations
     */
    public function down(): void
    {
        Schema::dropIfExists('lowongan_pekerjaan');
    }
};