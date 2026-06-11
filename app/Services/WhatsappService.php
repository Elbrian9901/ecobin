<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected string $url;
    protected string $apiKey;

    public function __construct()
    {
        $this->url    = config('services.whatsapp.url');
        $this->apiKey = config('services.whatsapp.key');
    }

    // Kirim pesan WA
    public function kirim(string $nomor, string $pesan): bool
    {
        try {
            Log::info('WA Kirim ke: ' . $nomor);
            Log::info('WA URL: ' . $this->url);
            Log::info('WA Key: ' . $this->apiKey);

            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => $this->apiKey,
                ])->post($this->url, [
                    'target'  => $nomor,
                    'message' => $pesan,
                ]);

            Log::info('WA Response: ' . $response->status() . ' - ' . $response->body());

            return $response->status() === 200;

        } catch (\Exception $e) {
            Log::error('WhatsApp gagal kirim: ' . $e->getMessage());
            return false;
        }
    }

    // Notifikasi 1 - Ditugaskan sebagai penanggung jawab
    public function notifikasiPenugasan(object $tong): bool
    {
        $pesan = "📋 *Notifikasi EcoBin*\n\n"
            . "Halo! Anda telah ditugaskan sebagai penanggung jawab tong sampah berikut:\n\n"
            . "🗑️ Nama   : {$tong->nama}\n"
            . "📍 Lokasi : {$tong->lokasi}\n"
            . "🔑 Kode   : {$tong->kode}\n\n"
            . "Mohon pantau tong sampah tersebut secara berkala.\n\n"
            . "Terima kasih 🙏\n"
            . "— EcoBin Monitoring System";

        return $this->kirim($tong->no_whatsapp, $pesan);
    }

    // Notifikasi 2 - Tong sampah penuh
    public function notifikasiPenuh(object $tong): bool
    {
        $pesan = "🚨 *Notifikasi EcoBin*\n\n"
            . "Halo! Tong sampah yang Anda kelola telah PENUH!\n\n"
            . "🗑️ Nama   : {$tong->nama}\n"
            . "📍 Lokasi : {$tong->lokasi}\n"
            . "🔑 Kode   : {$tong->kode}\n"
            . "📊 Level  : 100%\n\n"
            . "Mohon segera lakukan pengangkutan!\n\n"
            . "Terima kasih 🙏\n"
            . "— EcoBin Monitoring System";

        return $this->kirim($tong->no_whatsapp, $pesan);
    }
}