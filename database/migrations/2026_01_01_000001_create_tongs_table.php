<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ============================================================
// Tabel tongs  (tanpa kategori, tanpa baterai)
// Jalankan: php artisan migrate:fresh  (HAPUS semua data lama)
//       atau: php artisan migrate       (jika belum pernah migrate)
// ============================================================
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tongs', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();        // TSP-01, TSP-02, dst
            $table->string('nama', 100);
            $table->string('lokasi', 150)->nullable();
            $table->unsignedInteger('kapasitas')->default(60); // dalam Liter
            $table->unsignedTinyInteger('persen')->default(0); // 0–100 %
            $table->enum('status', ['normal','hampir_penuh','penuh'])->default('normal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tongs');
    }
};
