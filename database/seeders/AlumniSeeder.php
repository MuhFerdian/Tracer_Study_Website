<?php
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AlumniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $alumniRoleId = DB::table('role')->where('role_kode', 'ALM')->value('role_id');

        $kategoriIt = DB::table('kategori_profesi')->where('kategori_profesi', 'IT & Software')->value('kategori_profesi_id');
        $jenisSwasta = DB::table('jenis_instansi')->where('jenis_instansi', 'Swasta')->value('jenis_instansi_id');
        $profesiBackend = DB::table('profesi')
            ->where('kategori_profesi_id', $kategoriIt)
            ->where('profesi', 'Backend Developer')
            ->value('profesi_id');


        DB::table('atasan')->updateOrInsert(
            ['email_atasan' => 'atasan@example.com'],
            [
                'user_id' => null,
                'nama_atasan' => 'Supervisor Test',
                'nama_instansi' => 'PT Maju Jaya',
                'jabatan' => 'Engineering Manager',
                'no_hp_atasan' => '082345678901',
                'otp_code' => null,
                'isOtp' => false,
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );
        $atasanId = DB::table('atasan')->where('email_atasan', 'atasan@example.com')->value('atasan_id');

        $alumniUsernames = [
            [
                'username' => 'alumni_test',
                'nim' => '2021001',
                'nama_alumni' => 'Alumni Test',
                'email' => 'alumni@example.com',
                'no_hp' => '081234567890',
            ],
            [
                'username' => 'alumni_satu',
                'nim' => '2021002',
                'nama_alumni' => 'Dina Pramesti',
                'email' => 'dina.pramesti@example.com',
                'no_hp' => '081234567891',
            ],
            [
                'username' => 'alumni_dua',
                'nim' => '2021003',
                'nama_alumni' => 'Rizky Saputra',
                'email' => 'rizky.saputra@example.com',
                'no_hp' => '081234567892',
            ],
        ];

        foreach ($alumniUsernames as $item) {
            DB::table('users')->updateOrInsert(
                ['username' => $item['username']],
                [
                    'role_id' => $alumniRoleId,
                    'name' => $item['nama_alumni'],
                    'email' => $item['email'],
                    'password' => Hash::make('password123'),
                    'status' => 'pending',
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
            $userId = DB::table('users')->where('username', $item['username'])->value('id');

            DB::table('alumni')->updateOrInsert(
                ['nim' => $item['nim']],
                [
                    'user_id' => $userId,
                    'atasan_id' => $atasanId,
                    'jenis_instansi_id' => $jenisSwasta,
                    'kategori_profesi_id' => $kategoriIt,
                    'profesi_id' => $profesiBackend,
                    'nama_alumni' => $item['nama_alumni'],
                    'prodi' => 'Teknik Informatika',
                    'no_hp' => $item['no_hp'],
                    'email' => $item['email'],
                    'tanggal_lulus' => '2023-06-15',
                    'tanggal_kerja_pertama' => '2023-07-01',
                    'masa_tunggu' => 16,
                    'tanggal_mulai_instansi' => '2023-07-01',
                    'nama_instansi' => 'PT Maju Jaya',
                    'skala_instansi' => 'nasional',
                    'lokasi_instansi' => 'Surabaya',
                    'otp_code' => null,
                    'isOtp' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
