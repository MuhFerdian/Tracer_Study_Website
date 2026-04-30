<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PertanyaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $data = [
            'Kerjasama Tim',
            'Keahlian di bidang TI',
            'Kemampuan berbahasa asing (Inggris)',
            'Kemampuan berkomunikasi',
            'Pengembangan diri',
            'Kepemimpinan',
            'Etos Kerja',
            'Kompetensi yang dibutuhkan tapi belum dapat dipenuhi',
            'Saran untuk kurikulum program studi',
        ];

        foreach ($data as $pertanyaan) {
            DB::table('pertanyaan')->updateOrInsert(
                ['question_text' => $pertanyaan],
                ['updated_at' => $now, 'created_at' => $now]
            );
        }
    }
}
