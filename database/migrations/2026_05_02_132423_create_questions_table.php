<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();

            $table->string('kode_soal')->nullable(); // contoh: f8, f502
            $table->text('pertanyaan');

            $table->enum('type', ['single', 'multiple', 'text', 'scale', 'matrix']);

            $table->boolean('is_required')->default(true);

            $table->integer('urutan')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
