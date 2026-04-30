<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $database = DB::getDatabaseName();

        $fk = DB::selectOne(
            "SELECT REFERENCED_TABLE_NAME AS ref_table
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME = 'alumni'
               AND COLUMN_NAME = 'user_id'
               AND CONSTRAINT_NAME = 'alumni_user_id_foreign'",
            [$database]
        );

        if ($fk && $fk->ref_table !== 'users') {
            DB::statement('ALTER TABLE alumni DROP FOREIGN KEY alumni_user_id_foreign');
            DB::statement('ALTER TABLE alumni ADD CONSTRAINT alumni_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left empty to avoid restoring legacy foreign key to old table.
    }
};
