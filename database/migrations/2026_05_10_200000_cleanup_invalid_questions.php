<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Hapus pertanyaan yang tidak sesuai standar Kemendikbud.
     * Pertanyaan valid dari QuestionSeeder berjumlah 52 (urutan 1-52).
     * Row dengan urutan > 52 atau kode_soal tidak dikenal adalah data test/sampah.
     */
    public function up(): void
    {
        // Kode soal valid sesuai instrumen Kemendikbud
        $validKodeSoal = [
            'f8', 'f502', 'f503', 'f505',
            'f5a1', 'f5a2', 'f1101', 'f1102', 'f5b', 'f5c', 'f5d',
            'f18a', 'f18b', 'f18c', 'f18d',
            'f1201', 'f1202', 'f14', 'f15',
            'f1761', 'f1762', 'f1763', 'f1764', 'f1765', 'f1766',
            'f1767', 'f1768', 'f1769', 'f1770', 'f1771', 'f1772',
            'f1773', 'f1774',
            'f21', 'f22', 'f23', 'f24', 'f25', 'f26', 'f27',
            'f301', 'f302', 'f303',
            'f401-f416', 'f416',
            'f6', 'f7', 'f7a',
            'f1001', 'f1002',
            'f1601-f1614', 'f1614',
        ];

        // Ambil ID pertanyaan yang kode_soal-nya tidak valid
        $invalidIds = DB::table('questions')
            ->whereNotIn('kode_soal', $validKodeSoal)
            ->pluck('id');

        if ($invalidIds->isNotEmpty()) {
            // Hapus answer_details terkait
            $answerIds = DB::table('answers')
                ->whereIn('question_id', $invalidIds)
                ->pluck('id');

            if ($answerIds->isNotEmpty()) {
                DB::table('answer_details')->whereIn('answer_id', $answerIds)->delete();
                DB::table('answers')->whereIn('id', $answerIds)->delete();
            }

            // Hapus question_options dan question_details terkait
            DB::table('question_options')->whereIn('question_id', $invalidIds)->delete();
            DB::table('question_details')->whereIn('question_id', $invalidIds)->delete();

            // Hapus pertanyaan tidak valid
            DB::table('questions')->whereIn('id', $invalidIds)->delete();
        }
    }

    public function down(): void
    {
        // Tidak bisa di-rollback karena data sudah dihapus
        // Jalankan QuestionSeeder ulang jika diperlukan
    }
};
