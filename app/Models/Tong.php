<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Tong extends Model
{
    protected $fillable = [
        'kode', 'nama', 'lokasi', 'no_whatsapp', 'kapasitas', 'persen', 'status',
    ];
    public function riwayats()
    {
        return $this->hasMany(Riwayat::class);
    }
    public function notifikasis()
    {
        return $this->hasMany(Notifikasi::class);
    }
    // Label status untuk ditampilkan di view
    public function statusLabel(): string
    {
        return match($this->status) {
            'penuh'        => 'Penuh',
            'hampir_penuh' => 'Hampir Penuh',
            default        => 'Normal',
        };
    }
    // Warna CSS class berdasarkan status
    public function statusColor(): string
    {
        return match($this->status) {
            'penuh'        => 'red',
            'hampir_penuh' => 'amber',
            default        => 'green',
        };
    }
}