<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $fillable = ['tong_id', 'tipe', 'pesan', 'sudah_dibaca'];

    public function tong()
    {
        return $this->belongsTo(Tong::class);
    }
}
