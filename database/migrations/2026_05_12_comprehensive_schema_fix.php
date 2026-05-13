<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * COMPREHENSIVE DATA & SCHEMA FIX
     * 
     * 1. Fix NULL values in question_options.value (set = label)
     * 2. Reset urutan to sequential 1-based index
     * 3. Remove unused columns:
     *    - questions.group_label
     *    - question_details.field_code_a
     *    - question_details.field_code_b
     */
    public function up(): void
    {
        // ===== FIX question_options.value =====
        // Set value = label if value is NULL or empty
        DB::update(
            'UPDATE question_options SET value = label WHERE value IS NULL OR value = ""'
        );

        // Ensure urutan is sequential 1-based index per question
        $optionGroups = DB::table('question_options')
            ->orderBy('question_id')
            ->orderBy('id')
            ->get()
            ->groupBy('question_id');

        foreach ($optionGroups as $questionId => $options) {
            $options->each(function ($option, $key) {
                DB::table('question_options')
                    ->where('id', $option->id)
                    ->update(['urutan' => $key + 1]);
            });
        }

        // ===== CLEANUP UNUSED COLUMNS =====
        
        // Remove unused group_label from questions table
        if (Schema::hasColumn('questions', 'group_label')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->dropColumn('group_label');
            });
        }

        // Remove unused field_code_b from question_details table
        if (Schema::hasColumn('question_details', 'field_code_b')) {
            Schema::table('question_details', function (Blueprint $table) {
                $table->dropColumn('field_code_b');
            });
        }

        // Remove unused field_code_a from question_details table
        if (Schema::hasColumn('question_details', 'field_code_a')) {
            Schema::table('question_details', function (Blueprint $table) {
                $table->dropColumn('field_code_a');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore group_label to questions table
        if (!Schema::hasColumn('questions', 'group_label')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->string('group_label')->nullable()->after('kode_soal');
            });
        }

        // Restore field_code_b to question_details table
        if (!Schema::hasColumn('question_details', 'field_code_b')) {
            Schema::table('question_details', function (Blueprint $table) {
                $table->string('field_code_b')->nullable()->after('field_code_a');
            });
        }

        // Restore field_code_a to question_details table
        if (!Schema::hasColumn('question_details', 'field_code_a')) {
            Schema::table('question_details', function (Blueprint $table) {
                $table->string('field_code_a')->nullable()->after('item_label');
            });
        }

        // Revert value to NULL (for rollback)
        DB::update('UPDATE question_options SET value = NULL WHERE value = label');
    }
};
