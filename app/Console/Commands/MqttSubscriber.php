<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Tong;
use App\Models\Riwayat;
use App\Models\Notifikasi;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\Exceptions\MqttClientException;

class MqttSubscriber extends Command
{
    protected $signature   = 'app:mqtt-subscriber';
    protected $description = 'Subscribe to HiveMQ Cloud and process ESP32 sensor data';

    private const RECONNECT_DELAY = 5;

    public function handle(): void
    {
        $host     = '2c3fc3c82e8d4633b08f9ce57bfeb879.s1.eu.hivemq.cloud';
        $port     = 8883;
        $username = 'ecobin';
        $password = 'Ecobin123';

        while (true) {
            try {
                $this->runSubscriber($host, $port, $username, $password);
            } catch (MqttClientException $e) {
                $this->error('[MQTT] Connection lost: ' . $e->getMessage());
                Log::error('MqttSubscriber disconnected', ['error' => $e->getMessage()]);
                $this->warn('[MQTT] Reconnecting in ' . self::RECONNECT_DELAY . 's...');
                sleep(self::RECONNECT_DELAY);
            } catch (\Throwable $e) {
                $this->error('[ERROR] ' . $e->getMessage());
                Log::error('MqttSubscriber fatal error', ['error' => $e->getMessage()]);
                sleep(self::RECONNECT_DELAY);
            }
        }
    }

    private function runSubscriber(string $host, int $port, ?string $username, ?string $password): void
    {
        $clientId = 'laravel-ecobin-' . gethostname();

        $settings = (new ConnectionSettings)
            ->setUsername($username)
            ->setPassword($password)
            ->setUseTls(true)
            ->setTlsSelfSignedAllowed(true)
            ->setKeepAliveInterval(60)
            ->setConnectTimeout(30)
            ->setReconnectAutomatically(false);

        $mqtt = new MqttClient($host, $port, $clientId);
        $mqtt->connect($settings, true);

        $this->info('[MQTT] Connected! Waiting for ESP32 data...');

        $mqtt->subscribe('website/sensor', function (string $topic, string $message) {
            $this->processPayload($message);
        }, qualityOfService: 0);

        $mqtt->loop(allowSleep: true);
        $mqtt->disconnect();
    }

    private function processPayload(string $raw): void
    {
        $this->line('[' . now()->format('H:i:s') . '] Raw: ' . $raw);

        $data = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['kode'], $data['persen'])) {
            $this->warn('  [!] Invalid JSON, skipping: ' . $raw);
            return;
        }

        $kode   = strtoupper(trim($data['kode']));
        $persen = (int) $data['persen'];

        if ($persen < 0 || $persen > 100) {
            $this->warn('  [!] persen out of range (' . $persen . ') for ' . $kode . ', skipping.');
            return;
        }

        $tong = Tong::where('kode', $kode)->first();

        if (!$tong) {
            $this->warn('  [!] Tong not found: ' . $kode);
            return;
        }

        $status = match(true) {
            $persen >= 100 => 'penuh',
            $persen >= 80  => 'hampir_penuh',
            default        => 'normal',
        };

        $prevStatus = $tong->status;

        $tong->update(['persen' => $persen, 'status' => $status]);

        Riwayat::create([
            'tong_id' => $tong->id,
            'jenis'   => 'sensor',
            'level'   => $persen,
            'waktu'   => now(),
        ]);

        if ($status === 'penuh' && $prevStatus !== 'penuh') {
            Notifikasi::create([
                'tong_id' => $tong->id,
                'tipe'    => 'penuh',
                'pesan'   => 'Tong ' . $kode . ' telah mencapai kapasitas penuh!',
            ]);
            $this->warn('  [NOTIF] Tong ' . $kode . ' penuh!');
        }

        $label = match($status) {
            'penuh'        => '[PENUH]',
            'hampir_penuh' => '[HAMPIR PENUH]',
            default        => '[NORMAL]',
        };

        $this->info('  ' . $label . ' ' . $kode . ' -> ' . $persen . '% (' . $status . ')');
    }
}