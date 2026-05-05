<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisInstansiSeeder extends Seeder
{
    public function run(): void
    {
        $jenisInstansi = [
            ['kode' => '1', 'nama' => 'Instansi Pemerintah', 'deskripsi' => 'Lembaga pemerintah pusat, daerah, atau SKPD'],
            ['kode' => '2', 'nama' => 'BUMN/BUMD', 'deskripsi' => 'Badan Usaha Milik Negara atau Daerah'],
            ['kode' => '3', 'nama' => 'Institusi Multilateral', 'deskripsi' => 'Organisasi internasional atau multilateral'],
            ['kode' => '4', 'nama' => 'Organisasi Non-Profit', 'deskripsi' => 'LSM, NGO, atau organisasi sosial'],
            ['kode' => '5', 'nama' => 'Perusahaan Swasta', 'deskripsi' => 'Perusahaan swasta nasional atau multinasional'],
            ['kode' => '6', 'nama' => 'Wiraswasta', 'deskripsi' => 'Usaha sendiri atau berwiraswasta'],
            ['kode' => '7', 'nama' => 'Lainnya', 'deskripsi' => 'Instansi lainnya yang tidak termasuk kategori di atas'],
        ];

        foreach ($jenisInstansi as $jenis) {
            $jenis['created_at'] = now();
            $jenis['updated_at'] = now();
            DB::table('jenis_instansi')->insert($jenis);
        }
    }
}
