<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('answer_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('answer_id')->constrained()->cascadeOnDelete();

            $table->foreignId('option_id')->nullable()->constrained('question_options')->nullOnDelete();

            $table->text('value')->nullable(); // untuk text / scale

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answer_details');
    }
};
