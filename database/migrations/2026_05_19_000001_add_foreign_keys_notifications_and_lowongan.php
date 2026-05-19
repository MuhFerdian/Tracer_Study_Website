<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Bersihkan data orphan sebelum menambahkan FK ──────────────────
        // Hapus notifikasi yang user_id-nya tidak ada di tabel users
        DB::statement('
            DELETE FROM notifications
            WHERE user_id NOT IN (SELECT id FROM users)
        ');

        // Set dibuat_oleh = NULL untuk lowongan yang user-nya sudah tidak ada
        DB::statement('
            UPDATE lowongan_pekerjaan
            SET dibuat_oleh = NULL
            WHERE dibuat_oleh IS NOT NULL
              AND dibuat_oleh NOT IN (SELECT id FROM users)
        ');

        // ── 2. Tambah FK ke notifications ────────────────────────────────────
        Schema::table('notifications', function (Blueprint $table) {
            // Pastikan kolom user_id belum punya FK (aman dijalankan ulang)
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->cascadeOnDelete(); // hapus notifikasi jika user dihapus
        });

        // ── 3. Tambah FK ke lowongan_pekerjaan ───────────────────────────────
        Schema::table('lowongan_pekerjaan', function (Blueprint $table) {
            $table->foreign('dibuat_oleh')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete(); // set NULL jika user pembuat dihapus
        });
    }

    public function down(): void
    {
        Schema::table('lowongan_pekerjaan', function (Blueprint $table) {
            $table->dropForeign(['dibuat_oleh']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
