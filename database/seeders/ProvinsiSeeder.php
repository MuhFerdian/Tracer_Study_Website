<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinsiSeeder extends Seeder
{
    public function run(): void
    {
        // Data Provinsi Indonesia (sampel)
        $provinsi = [
            ['kode' => '11', 'nama' => 'Aceh'],
            ['kode' => '12', 'nama' => 'Sumatera Utara'],
            ['kode' => '13', 'nama' => 'Sumatera Barat'],
            ['kode' => '14', 'nama' => 'Riau'],
            ['kode' => '15', 'nama' => 'Jambi'],
            ['kode' => '16', 'nama' => 'Sumatera Selatan'],
            ['kode' => '17', 'nama' => 'Bengkulu'],
            ['kode' => '18', 'nama' => 'Lampung'],
            ['kode' => '19', 'nama' => 'Kepulauan Bangka Belitung'],
            ['kode' => '21', 'nama' => 'Jawa Barat'],
            ['kode' => '31', 'nama' => 'DKI Jakarta'],
            ['kode' => '32', 'nama' => 'Jawa Tengah'],
            ['kode' => '33', 'nama' => 'Daerah Istimewa Yogyakarta'],
            ['kode' => '34', 'nama' => 'Jawa Timur'],
            ['kode' => '35', 'nama' => 'Banten'],
            ['kode' => '36', 'nama' => 'Bali'],
            ['kode' => '52', 'nama' => 'Nusa Tenggara Barat'],
            ['kode' => '53', 'nama' => 'Nusa Tenggara Timur'],
            ['kode' => '61', 'nama' => 'Kalimantan Barat'],
            ['kode' => '62', 'nama' => 'Kalimantan Tengah'],
            ['kode' => '63', 'nama' => 'Kalimantan Selatan'],
            ['kode' => '64', 'nama' => 'Kalimantan Timur'],
            ['kode' => '65', 'nama' => 'Kalimantan Utara'],
            ['kode' => '71', 'nama' => 'Sulawesi Utara'],
            ['kode' => '72', 'nama' => 'Sulawesi Tengah'],
            ['kode' => '73', 'nama' => 'Sulawesi Selatan'],
            ['kode' => '74', 'nama' => 'Sulawesi Tenggara'],
            ['kode' => '75', 'nama' => 'Gorontalo'],
            ['kode' => '76', 'nama' => 'Sulawesi Barat'],
            ['kode' => '81', 'nama' => 'Maluku'],
            ['kode' => '82', 'nama' => 'Maluku Utara'],
            ['kode' => '91', 'nama' => 'Papua Barat'],
            ['kode' => '92', 'nama' => 'Papua'],
            ['kode' => '94', 'nama' => 'Papua Barat Daya'],
            ['kode' => '95', 'nama' => 'Papua Tengah'],
            ['kode' => '96', 'nama' => 'Papua Pegunungan'],
        ];

        foreach ($provinsi as $prov) {
            $prov['created_at'] = now();
            $prov['updated_at'] = now();
            DB::table('provinsi')->insert($prov);
        }

        // Data Kota Kabupaten (sampel untuk Jawa Timur)
        $kotaJawaTimur = [
            ['provinsi_id' => 14, 'kode' => '3501', 'nama' => 'Kota Surabaya'],
            ['provinsi_id' => 14, 'kode' => '3502', 'nama' => 'Kabupaten Sidoarjo'],
            ['provinsi_id' => 14, 'kode' => '3503', 'nama' => 'Kabupaten Gresik'],
            ['provinsi_id' => 14, 'kode' => '3504', 'nama' => 'Kabupaten Bangkalan'],
            ['provinsi_id' => 14, 'kode' => '3505', 'nama' => 'Kabupaten Madura'],
            ['provinsi_id' => 14, 'kode' => '3506', 'nama' => 'Kota Mojokerto'],
            ['provinsi_id' => 14, 'kode' => '3507', 'nama' => 'Kota Pasuruan'],
            ['provinsi_id' => 14, 'kode' => '3508', 'nama' => 'Kabupaten Pasuruan'],
            ['provinsi_id' => 14, 'kode' => '3509', 'nama' => 'Kabupaten Probolinggo'],
            ['provinsi_id' => 14, 'kode' => '3510', 'nama' => 'Kota Probolinggo'],
            ['provinsi_id' => 14, 'kode' => '3511', 'nama' => 'Kabupaten Lumajang'],
            ['provinsi_id' => 14, 'kode' => '3512', 'nama' => 'Kabupaten Jember'],
            ['provinsi_id' => 14, 'kode' => '3513', 'nama' => 'Kabupaten Banyuwangi'],
            ['provinsi_id' => 14, 'kode' => '3514', 'nama' => 'Kota Batu'],
            ['provinsi_id' => 14, 'kode' => '3515', 'nama' => 'Kabupaten Blitar'],
            ['provinsi_id' => 14, 'kode' => '3516', 'nama' => 'Kota Blitar'],
            ['provinsi_id' => 14, 'kode' => '3517', 'nama' => 'Kabupaten Kediri'],
            ['provinsi_id' => 14, 'kode' => '3518', 'nama' => 'Kota Kediri'],
            ['provinsi_id' => 14, 'kode' => '3519', 'nama' => 'Kabupaten Malang'],
            ['provinsi_id' => 14, 'kode' => '3520', 'nama' => 'Kota Malang'],
            ['provinsi_id' => 14, 'kode' => '3521', 'nama' => 'Kabupaten Ngawi'],
            ['provinsi_id' => 14, 'kode' => '3522', 'nama' => 'Kabupaten Bojonegoro'],
            ['provinsi_id' => 14, 'kode' => '3523', 'nama' => 'Kabupaten Tuban'],
            ['provinsi_id' => 14, 'kode' => '3524', 'nama' => 'Kabupaten Lamongan'],
            ['provinsi_id' => 14, 'kode' => '3525', 'nama' => 'Kota Madiun'],
            ['provinsi_id' => 14, 'kode' => '3526', 'nama' => 'Kabupaten Madiun'],
            ['provinsi_id' => 14, 'kode' => '3527', 'nama' => 'Kabupaten Magetan'],
            ['provinsi_id' => 14, 'kode' => '3528', 'nama' => 'Kabupaten Ponorogo'],
            ['provinsi_id' => 14, 'kode' => '3529', 'nama' => 'Kota Surabaya'],
        ];

        foreach ($kotaJawaTimur as $kota) {
            $kota['created_at'] = now();
            $kota['updated_at'] = now();
            DB::table('kota_kabupaten')->insert($kota);
        }
    }
}
