<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Refactor role system dari (Admin, Alumni, Atasan) 
     * menjadi (Admin, Dosen, Alumni)
     * 
     * - Admin: full access (dashboard, CRUD, reports)
     * - Dosen: full access (same as admin, untuk dosen/pengajar)
     * - Alumni: limited access (hanya isi kuisioner)
     */
    public function up(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // 1. Delete users with Atasan role (role_id = 3)
            $atasanRoleId = DB::table('role')->where('role_kode', 'ATS')->value('role_id');
            if ($atasanRoleId) {
                DB::table('users')->where('role_id', $atasanRoleId)->delete();
            }

            // 2. Delete Atasan role (role_id = 3)
            DB::table('role')->where('role_kode', 'ATS')->delete();

            // 3. Update Alumni role
            DB::table('role')
                ->where('role_kode', 'ALM')
                ->update([
                    'role_nama' => 'Alumni',
                    'role_kode' => 'ALM'
                ]);

            // 4. Insert Dosen role
            DB::table('role')->insert([
                'role_kode' => 'DSN',
                'role_nama' => 'Dosen',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 5. Update Admin role
            DB::table('role')
                ->where('role_kode', 'ADM')
                ->update([
                    'role_nama' => 'Admin',
                    'role_kode' => 'ADM'
                ]);

            // 5b. Ensure users.role_id foreign key exists after role table is ready
            if (Schema::hasTable('users')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->foreign('role_id')->references('role_id')->on('role');
                });
            }

            // 6. Remove atasan_id references from alumni (optional - keep for historical data)
            if (Schema::hasTable('alumni') && Schema::hasColumn('alumni', 'atasan_id')) {
                if (!Schema::hasColumn('alumni', 'requires_supervisor')) {
                    Schema::table('alumni', function (Blueprint $table) {
                        $table->boolean('requires_supervisor')->default(false)->after('atasan_id');
                    });
                }
            }
        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // Restore Atasan role
            DB::table('role')->insert([
                'role_kode' => 'ATS',
                'role_nama' => 'Atasan',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Remove Dosen role
            DB::table('role')->where('role_kode', 'DSN')->delete();

            // Remove new column if added
            if (Schema::hasTable('alumni') && Schema::hasColumn('alumni', 'requires_supervisor')) {
                Schema::table('alumni', function (Blueprint $table) {
                    $table->dropColumn('requires_supervisor');
                });
            }

            if (Schema::hasTable('users')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropForeign(['role_id']);
                });
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
};
