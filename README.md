<p align="center">
  <a href="https://polije.ac.id" target="_blank">
    <img src="public/assets/img/Logo_Polije_Blue.png" width="200" alt="Polije Logo">
  </a>
</p>

# 📊 Tracer Study - PSDKU Nganjuk

> Sistem Informasi Tracer Study untuk melacak dan memonitor perkembangan karir alumni Politeknik Negeri Jember (Polije) PSDKU Nganjuk

## 📋 Tentang Project

**Tracer Study** adalah sistem informasi yang bertujuan untuk melacak atau mengidentifikasi jejak perkembangan karir alumni setelah mereka lulus dari Politeknik Negeri Jember PSDKU Nganjuk.

### Latar Belakang

Sebelumnya, JTI telah memiliki sistem informasi tracer study namun sistem tersebut **tidak dapat mengakomodasi kebutuhan instrumen akreditasi baru dari LAM INFOKOM**. Oleh karena itu, diperlukan pengembangan sistem baru yang dapat:

- ✅ Mengelola informasi data lulusan (alumni)
- ✅ Mengelola data pengguna lulusan (atasan/instansi)
- ✅ Melakukan penilaian kinerja lulusan
- ✅ Menyediakan data untuk kebutuhan akreditasi
- ✅ Menghasilkan laporan dan ekspor data yang akurat

## 🚀 Fitur Utama

| Fitur | Deskripsi |
|-------|-------------|
| 👨‍🎓 **Manajemen Alumni** | Kelola data alumni, riwayat pendidikan, dan pekerjaan |
| 🏢 **Manajemen Dosen** | Pendataan dosen |
| 📝 **Kuesioner Survey** | Pengisian survey oleh alumni dan atasan |
| 📊 **Laporan & Ekspor** | Generate laporan dalam format Excel/PDF |
| 👑 **Role Management** | Multi-level akses (Admin, Dosen, Alumni) |
| 🔐 **Autentikasi** | Sistem login yang aman untuk setiap role |

## 🛠️ Tech Stack

| Kategori | Teknologi |
|----------|-----------|
| **Backend** | Laravel 12.x |
| **Frontend** | Blade Template, Tailwind CSS, Bootstrap 5 |
| **Database** | MySQL |
| **Authentication** | Laravel Breeze / Custom |
| **Export** | Laravel Excel |
| **Development** | PHP 8.4+, Composer, Node.js |

## 📁 Struktur Database

| Tabel | Fungsi |
|-------|--------|
| `alumni` | Data alumni |
| `kategori_profesi` | Kategori bidang pekerjaan |
| `dosen` | Daftar nama dosen |
| `pertanyaan` | Daftar pertanyaan survey |
| `jawaban` | Jawaban survey dari alumni |
| `users` | Data pengguna untuk autentikasi |
| `roles` | Data role (Admin, Dosen, Alumni) |

## 💻 Instalasi & Setup

### Prasyarat

- PHP >= 8.4
- Composer
- Node.js & NPM
- MySQL

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/MuhFerdian/Tracer_Study_Website.git

# 2. Masuk ke folder project
cd Tracer_Study_Website

# 3. Install dependencies PHP
composer install

# 4. Install dependencies Node.js
npm install

# 5. Copy file environment
cp .env.example .env

# 6. Generate application key
php artisan key:generate

# 7. Konfigurasi database di file .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_kamu
DB_USERNAME=root
DB_PASSWORD=

# 8. Jalankan migration
php artisan migrate

# 9. Jalankan seeder (data master)
php artisan db:seed --class=MasterDataSeeder
php artisan db:seed --class=DatabaseSeeder

# 10. Build assets frontend
npm run build

# 11. Jalankan server
php artisan serve