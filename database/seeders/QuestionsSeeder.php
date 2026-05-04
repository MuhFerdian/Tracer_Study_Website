<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $questions = [
            [
                'kode' => 'Q1',
                'pertanyaan' => 'Status Anda saat ini?',
                'type' => 'single',
                'options' => ['Bekerja', 'Belum Bekerja', 'Wirausaha', 'Studi Lanjut']
            ],
            [
                'kode' => 'Q2',
                'pertanyaan' => 'Seberapa relevan pekerjaan Anda dengan bidang studi?',
                'type' => 'single',
                'options' => ['Sangat Relevan', 'Cukup Relevan', 'Kurang Relevan', 'Tidak Relevan']
            ],
            [
                'kode' => 'Q3',
                'pertanyaan' => 'Berapa lama masa tunggu kerja setelah lulus?',
                'type' => 'single',
                'options' => ['< 3 bulan', '3-6 bulan', '6-12 bulan', '> 1 tahun']
            ],
            [
                'kode' => 'Q4',
                'pertanyaan' => 'Skill apa yang paling dibutuhkan di pekerjaan Anda?',
                'type' => 'multiple',
                'options' => ['Programming', 'Komunikasi', 'Manajemen', 'Problem Solving']
            ],
        ];

        foreach ($questions as $index => $q) {

            // insert question
            $questionId = DB::table('questions')->insertGetId([
                'kode' => $q['kode'],
                'pertanyaan' => $q['pertanyaan'],
                'type' => $q['type'],
                'is_required' => true,
                'urutan' => $index + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // insert options
            foreach ($q['options'] as $i => $opt) {
                DB::table('question_options')->insert([
                    'question_id' => $questionId,
                    'label' => $opt,
                    'value' => $i + 1,
                    'urutan' => $i + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}