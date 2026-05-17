<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_periods', function (Blueprint $table) {
            $table->id();
            $table->string('nama');                        // "Tracer Study 2025"
            $table->year('tahun');                         // 2025
            $table->date('tanggal_buka');
            $table->date('tanggal_tutup');
            $table->enum('status', ['aktif', 'tutup', 'draft'])->default('draft');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // Tambah kolom survey_period_id ke tabel answers
        Schema::table('answers', function (Blueprint $table) {
            $table->foreignId('survey_period_id')
                  ->nullable()
                  ->after('question_id')
                  ->constrained('survey_periods')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->dropForeign(['survey_period_id']);
            $table->dropColumn('survey_period_id');
        });

        Schema::dropIfExists('survey_periods');
    }
};
