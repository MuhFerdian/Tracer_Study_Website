<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fix jawaban matrix yang tersimpan sebagai nested JSON.
 *
 * Data lama (salah):
 *   answer_details.value = {"anjay":"{\"gacor\":\"4\",\"anjay\":\"1\",\"bagus\":\"3\"}","gacor":null,"bagus":null}
 *
 * Data yang benar:
 *   answer_details.value = {"anjay":"1","gacor":"4","bagus":"3"}
 */
return new class extends Migration
{
    public function up(): void
    {
        // Ambil semua answer_details yang terkait pertanyaan matrix
        $matrixQuestionIds = DB::table('questions')
            ->where('type', 'matrix')
            ->pluck('id');

        if ($matrixQuestionIds->isEmpty()) {
            return;
        }

        $details = DB::table('answer_details as ad')
            ->join('answers as a', 'a.id', '=', 'ad.answer_id')
            ->whereIn('a.question_id', $matrixQuestionIds)
            ->whereNotNull('ad.value')
            ->where('ad.value', '!=', '')
            ->select('ad.id', 'ad.value')
            ->get();

        foreach ($details as $detail) {
            $decoded = json_decode($detail->value, true);
            if (!is_array($decoded)) continue;

            // Cek apakah nested: value dari key pertama adalah JSON object
            $firstVal = reset($decoded);
            if (!is_string($firstVal) || strlen($firstVal) === 0 || $firstVal[0] !== '{') {
                continue; // Sudah format benar, skip
            }

            $innerDecoded = json_decode($firstVal, true);
            if (!is_array($innerDecoded)) continue;

            // Update ke format flat yang benar
            DB::table('answer_details')
                ->where('id', $detail->id)
                ->update(['value' => json_encode($innerDecoded)]);
        }
    }

    public function down(): void
    {
        // Tidak bisa di-rollback karena data lama sudah tidak bisa dibedakan
    }
};
