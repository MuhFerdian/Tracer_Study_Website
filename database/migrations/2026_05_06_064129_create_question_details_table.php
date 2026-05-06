<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('question_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->string('item_label'); // Label dari item, misal: "Etika", "Bahasa Inggris"
            $table->string('field_code_a')->nullable(); // Kode field kolom A, misal: f1761
            $table->string('field_code_b')->nullable(); // Kode field kolom B, misal: f1762
            $table->integer('urutan')->default(0); // Urutan item
            $table->timestamps();

            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->index('question_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_details');
    }
};
