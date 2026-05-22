<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel riwayat
        Schema::create('riwayats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tong_id')->constrained('tongs')->cascadeOnDelete();
            $table->enum('jenis', ['sensor','pengangkutan','tong_penuh']);
            $table->unsignedTinyInteger('level')->default(0);   // % kapasitas saat event
            $table->decimal('berat', 6, 2)->nullable();         // kg (opsional, dari ESP32)
            $table->timestamp('waktu')->useCurrent();
            $table->timestamps();
        });

        // Tabel notifikasi
        Schema::create('notifikasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tong_id')->constrained('tongs')->cascadeOnDelete();
            $table->enum('tipe', ['penuh','hampir_penuh']);
            $table->string('pesan', 255);
            $table->boolean('sudah_dibaca')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasis');
        Schema::dropIfExists('riwayats');
    }
};
