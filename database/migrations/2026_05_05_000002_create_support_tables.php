<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tabel provinsi dan kota_kabupaten dihapus karena tidak digunakan
return new class extends Migration
{
    public function up(): void
    {
        // Tidak ada tabel yang dibuat
    }

    public function down(): void
    {
        // Tidak ada tabel yang di-drop
    }
};
