<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->unsignedBigInteger('role_id')->nullable()->after('id');
                $table->index('role_id', 'users_role_id_index');
            }

            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username', 50)->nullable()->after('role_id');
                $table->unique('username', 'users_username_unique');
            }

            if (!Schema::hasColumn('users', 'nim')) {
                $table->string('nim')->nullable()->after('name');
                $table->unique('nim', 'users_nim_unique');
            }

            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('pending')->after('password');
            }

            if (!Schema::hasColumn('users', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'tanggal_lahir')) {
                $table->dropColumn('tanggal_lahir');
            }

            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('users', 'nim')) {
                $table->dropUnique('users_nim_unique');
                $table->dropColumn('nim');
            }

            if (Schema::hasColumn('users', 'username')) {
                $table->dropUnique('users_username_unique');
                $table->dropColumn('username');
            }

            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropIndex('users_role_id_index');
                $table->dropColumn('role_id');
            }
        });
    }
};
