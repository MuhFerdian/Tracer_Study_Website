<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $database = DB::getDatabaseName();

        // Find existing foreign key constraint for alumni.user_id (if any)
        $fk = DB::selectOne(
            "SELECT CONSTRAINT_NAME as constraint_name
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME = 'alumni'
               AND COLUMN_NAME = 'user_id'
               AND REFERENCED_TABLE_NAME IS NOT NULL",
            [$database]
        );

        if ($fk && !empty($fk->constraint_name)) {
            DB::statement(sprintf('ALTER TABLE alumni DROP FOREIGN KEY `%s`', $fk->constraint_name));
        }

        // Modify column to allow NULL (MySQL syntax)
        DB::statement("ALTER TABLE alumni MODIFY user_id BIGINT UNSIGNED NULL");

        // Add FK with ON DELETE SET NULL
        DB::statement("ALTER TABLE alumni ADD CONSTRAINT alumni_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL");
    }

    public function down(): void
    {
        $database = DB::getDatabaseName();

        $fk = DB::selectOne(
            "SELECT CONSTRAINT_NAME as constraint_name
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME = 'alumni'
               AND COLUMN_NAME = 'user_id'
               AND REFERENCED_TABLE_NAME IS NOT NULL",
            [$database]
        );

        if ($fk && !empty($fk->constraint_name)) {
            DB::statement(sprintf('ALTER TABLE alumni DROP FOREIGN KEY `%s`', $fk->constraint_name));
        }

        DB::statement("ALTER TABLE alumni MODIFY user_id BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE alumni ADD CONSTRAINT alumni_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
    }
};
