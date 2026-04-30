<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cek apakah kolom 'pertanyaan' masih ada
        if (Schema::hasColumn('pertanyaan', 'pertanyaan')) {
            
            // Pindahkan data dari 'pertanyaan' ke 'question_text' 
            // (kalau 'question_text' kosong)
            DB::table('pertanyaan')
                ->whereNull('question_text')
                ->orWhere('question_text', '')
                ->update([
                    'question_text' => DB::raw('pertanyaan')
                ]);
            
            // Hapus kolom 'pertanyaan'
            Schema::table('pertanyaan', function (Blueprint $table) {
                $table->dropColumn('pertanyaan');
            });
        }
    }

    public function down(): void
    {
        // Kalau rollback, bikin lagi kolom 'pertanyaan'
        Schema::table('pertanyaan', function (Blueprint $table) {
            $table->text('pertanyaan')->nullable()->after('question_text');
        });
        
        // Restore data (opsional)
        DB::table('pertanyaan')->update([
            'pertanyaan' => DB::raw('question_text')
        ]);
    }
};