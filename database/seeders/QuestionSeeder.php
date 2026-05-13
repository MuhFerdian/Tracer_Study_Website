<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * ⚠️ SEEDER DIKOSONGKAN - Database siap untuk diisi manual!
     * 
     * Pertanyaan harus diisi oleh Admin/Dosen melalui:
     * 1. Form CRUD → Admin Panel → Manajemen Pertanyaan
     * 2. Import Excel → Download template dari Panduan Pertanyaan
     * 3. Ikuti panduan PDF Kemendikbud yang tersedia
     * 
     * Hanya data awal yang di-clear untuk memastikan database fresh.
     */
    public function run(): void
    {
        // Clear existing data to start fresh
        DB::table('answer_details')->delete();
        DB::table('answers')->delete();
        DB::table('question_details')->delete();
        DB::table('question_options')->delete();
        DB::table('questions')->delete();

        // Database kosong - siap untuk admin/dosen isi pertanyaan
    }
}
