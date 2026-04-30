<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        // Create Admin role
        DB::table('role')->updateOrInsert(
            ['role_kode' => 'ADM'],
            ['role_nama' => 'Admin', 'updated_at' => $now, 'created_at' => $now]
        );

        // Create Dosen role
        DB::table('role')->updateOrInsert(
            ['role_kode' => 'DSN'],
            ['role_nama' => 'Dosen', 'updated_at' => $now, 'created_at' => $now]
        );

        // Create Alumni role
        DB::table('role')->updateOrInsert(
            ['role_kode' => 'ALM'],
            ['role_nama' => 'Alumni', 'updated_at' => $now, 'created_at' => $now]
        );

        // Remove Atasan role jika ada
        DB::table('role')->where('role_kode', 'ATS')->delete();

        $adminRoleId = DB::table('role')->where('role_kode', 'ADM')->value('role_id');
        $dosenRoleId = DB::table('role')->where('role_kode', 'DSN')->value('role_id');

        // Create Admin user
        DB::table('users')->updateOrInsert(
            ['username' => 'admin'],
            [
                'role_id' => $adminRoleId,
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
                'status' => 'pending',
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        // Create Dosen user (sample)
        DB::table('users')->updateOrInsert(
            ['username' => 'dosen'],
            [
                'role_id' => $dosenRoleId,
                'name' => 'Dosen',
                'email' => 'dosen@example.com',
                'password' => Hash::make('password123'),
                'status' => 'pending',
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );
    }
}
