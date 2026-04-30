<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Upgrade PBL_TracerStudy schema untuk compatibility dengan Flutter API
     * Merge struktur dari tracer_study_web-aldo yang sudah tested
     */
    public function up(): void
    {
        // 1. Upgrade users table - add fields dari Flutter API
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'username')) {
                    $table->string('username')->unique()->after('role_id');
                }
                if (!Schema::hasColumn('users', 'nim')) {
                    $table->string('nim')->nullable()->after('username');
                }
                if (!Schema::hasColumn('users', 'email')) {
                    $table->string('email')->nullable()->unique()->after('nim');
                }
                if (!Schema::hasColumn('users', 'status')) {
                    $table->string('status')->default('pending')->after('password');
                }
                if (!Schema::hasColumn('users', 'tanggal_lahir')) {
                    $table->date('tanggal_lahir')->nullable()->after('status');
                }
            });
        }

        // 2. Upgrade pertanyaan table - add fields untuk flexible survey
        if (Schema::hasTable('pertanyaan')) {
            Schema::table('pertanyaan', function (Blueprint $table) {
                if (!Schema::hasColumn('pertanyaan', 'kode_soal')) {
                    $table->string('kode_soal')->nullable()->after('pertanyaan_id');
                }
                if (!Schema::hasColumn('pertanyaan', 'question_text')) {
                    // question_text bisa copy dari pertanyaan column atau create baru
                    $table->text('question_text')->nullable()->after('kode_soal');
                }
                if (!Schema::hasColumn('pertanyaan', 'type')) {
                    // type: text, radio, checkbox, number
                    $table->string('type')->default('text')->after('question_text');
                }
                if (!Schema::hasColumn('pertanyaan', 'options')) {
                    // untuk radio/checkbox - store as JSON
                    $table->text('options')->nullable()->after('type');
                }
                if (!Schema::hasColumn('pertanyaan', 'urutan')) {
                    // urutan pertanyaan
                    $table->integer('urutan')->default(0)->after('options');
                }
            });
        }

        // 3. Ensure users table has required fields for Sanctum API
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'name')) {
                    $table->string('name')->nullable()->after('id');
                }
                if (!Schema::hasColumn('users', 'nim')) {
                    $table->string('nim')->nullable()->unique()->after('name');
                }
                if (!Schema::hasColumn('users', 'status')) {
                    $table->string('status')->default('pending')->after('password');
                }
                if (!Schema::hasColumn('users', 'tanggal_lahir')) {
                    $table->date('tanggal_lahir')->nullable()->after('status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus columns yang ditambah
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumnIfExists('username');
                $table->dropColumnIfExists('nim');
                $table->dropColumnIfExists('email');
                $table->dropColumnIfExists('status');
                $table->dropColumnIfExists('tanggal_lahir');
            });
        }

        if (Schema::hasTable('pertanyaan')) {
            Schema::table('pertanyaan', function (Blueprint $table) {
                $table->dropColumnIfExists('kode_soal');
                $table->dropColumnIfExists('question_text');
                $table->dropColumnIfExists('type');
                $table->dropColumnIfExists('options');
                $table->dropColumnIfExists('urutan');
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumnIfExists('name');
                $table->dropColumnIfExists('username');
                $table->dropColumnIfExists('nim');
                $table->dropColumnIfExists('email');
                $table->dropColumnIfExists('status');
                $table->dropColumnIfExists('tanggal_lahir');
            });
        }
    }
};
