<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Riwayat extends Model
{
    protected $fillable = ['tong_id', 'jenis', 'level', 'berat', 'waktu'];
    protected $casts    = ['waktu' => 'datetime'];

    public function tong()
    {
        return $this->belongsTo(Tong::class);
    }
}
