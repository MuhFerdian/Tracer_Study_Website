<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AlumniSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // ambil role alumni
        $roleId = DB::table('role')->where('role_kode', 'ALM')->value('role_id');

        $data = [
            [
                'username' => 'alumni1',
                'nim' => 'E2021001',
                'nama' => 'Alumni Satu',
                'email' => 'alumni1@mail.com',
                'alamat' => 'Jl. Contoh No. 1',
                'no_hp' => '0811111111',
            ],
            [
                'username' => 'alumni2',
                'nim' => 'E2021002',
                'nama' => 'Alumni Dua',
                'email' => 'alumni2@mail.com',
                'almat' => 'Jl. Contoh No. 2',
                'no_hp' => '0822222222',
            ],
        ];

        foreach ($data as $item) {

            // insert user
            DB::table('users')->updateOrInsert(
                ['username' => $item['username']],
                [
                    'role_id' => $roleId,
                    'name' => $item['nama'],
                    'email' => $item['email'],
                    'password' => Hash::make('password123'),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $userId = DB::table('users')->where('username', $item['username'])->value('id');

            // insert alumni (VERSI BARU)
            DB::table('alumni')->updateOrInsert(
                ['nim' => $item['nim']],
                [
                    'user_id' => $userId,
                    'nama' => $item['nama'],
                    'prodi' => 'Teknik Informatika',
                    'no_hp' => $item['no_hp'],
                    'email' => $item['email'],
                    'tahun_lulus' => 2023,
                    'status_pekerjaan' => 'Bekerja',
                    'nama_instansi' => 'PT Contoh',
                    'posisi' => 'Backend Developer',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}