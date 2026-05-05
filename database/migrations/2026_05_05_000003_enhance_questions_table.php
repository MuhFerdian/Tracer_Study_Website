<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Enhance questions table jika diperlukan
        if (!Schema::hasColumn('questions', 'deskripsi')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->text('deskripsi')->nullable()->after('pertanyaan');
            });
        }
        if (!Schema::hasColumn('questions', 'tipe_data')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->string('tipe_data')->default('text')->after('type')->comment('text, number, date, year');
            });
        }

        // Update questions yang sudah ada dengan kode PDF yang sesuai
        DB::table('questions')->where('id', 1)->update(['kode_soal' => 'f8']);
        DB::table('questions')->where('id', 2)->update(['kode_soal' => 'f14']);
        DB::table('questions')->where('id', 3)->update(['kode_soal' => 'f502']);
        DB::table('questions')->where('id', 4)->update(['kode_soal' => 'f1767']);
    }

    public function down(): void
    {
        if (Schema::hasColumn('questions', 'deskripsi')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->dropColumn('deskripsi');
            });
        }
        if (Schema::hasColumn('questions', 'tipe_data')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->dropColumn('tipe_data');
            });
        }
    }
};
