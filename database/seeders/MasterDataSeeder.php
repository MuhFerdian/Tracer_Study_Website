<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $jenisInstansi = ['Pemerintah', 'BUMN', 'Swasta', 'NGO'];
        foreach ($jenisInstansi as $item) {
            DB::table('jenis_instansi')->updateOrInsert(
                ['jenis_instansi' => $item],
                ['updated_at' => $now, 'created_at' => $now]
            );
        }

        $kategoriProfesi = ['IT & Software', 'Teknik', 'Administrasi', 'Pendidikan', 'Lainnya'];
        foreach ($kategoriProfesi as $item) {
            DB::table('kategori_profesi')->updateOrInsert(
                ['kategori_profesi' => $item],
                ['updated_at' => $now, 'created_at' => $now]
            );
        }

        // Get kategori IDs
        $itData = DB::table('kategori_profesi')->where('kategori_profesi', 'IT & Software')->first();
        $teknikData = DB::table('kategori_profesi')->where('kategori_profesi', 'Teknik')->first();
        $adminData = DB::table('kategori_profesi')->where('kategori_profesi', 'Administrasi')->first();

        // Insert Profesi for IT & Software
        $profesiIT = [
            ['kategori_profesi_id' => $itData->kategori_profesi_id, 'profesi' => 'Full Stack Developer'],
            ['kategori_profesi_id' => $itData->kategori_profesi_id, 'profesi' => 'Backend Developer'],
            ['kategori_profesi_id' => $itData->kategori_profesi_id, 'profesi' => 'Frontend Developer'],
            ['kategori_profesi_id' => $itData->kategori_profesi_id, 'profesi' => 'Database Administrator'],
            ['kategori_profesi_id' => $itData->kategori_profesi_id, 'profesi' => 'System Administrator'],
        ];
        foreach ($profesiIT as $item) {
            DB::table('profesi')->updateOrInsert(
                ['kategori_profesi_id' => $item['kategori_profesi_id'], 'profesi' => $item['profesi']],
                ['updated_at' => $now, 'created_at' => $now]
            );
        }

        // Insert Profesi for Teknik
        $profesiTeknik = [
            ['kategori_profesi_id' => $teknikData->kategori_profesi_id, 'profesi' => 'Insinyur Mesin'],
            ['kategori_profesi_id' => $teknikData->kategori_profesi_id, 'profesi' => 'Insinyur Sipil'],
            ['kategori_profesi_id' => $teknikData->kategori_profesi_id, 'profesi' => 'Insinyur Elektro'],
            ['kategori_profesi_id' => $teknikData->kategori_profesi_id, 'profesi' => 'Insinyur Industri'],
        ];
        foreach ($profesiTeknik as $item) {
            DB::table('profesi')->updateOrInsert(
                ['kategori_profesi_id' => $item['kategori_profesi_id'], 'profesi' => $item['profesi']],
                ['updated_at' => $now, 'created_at' => $now]
            );
        }

        // Insert Profesi for Administrasi
        $profesiAdmin = [
            ['kategori_profesi_id' => $adminData->kategori_profesi_id, 'profesi' => 'Admin Kantor'],
            ['kategori_profesi_id' => $adminData->kategori_profesi_id, 'profesi' => 'Staff HRD'],
            ['kategori_profesi_id' => $adminData->kategori_profesi_id, 'profesi' => 'Staff Keuangan'],
        ];
        foreach ($profesiAdmin as $item) {
            DB::table('profesi')->updateOrInsert(
                ['kategori_profesi_id' => $item['kategori_profesi_id'], 'profesi' => $item['profesi']],
                ['updated_at' => $now, 'created_at' => $now]
            );
        }
    }
}
