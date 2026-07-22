<?php

namespace Tests\Feature;

use App\Models\Tong;
use App\Models\Riwayat;
use App\Models\Notifikasi;
use App\Models\User;
use App\Services\WhatsappService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TongSampahTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock WhatsappService supaya tidak benar-benar kirim WA saat test
        $this->mock(WhatsappService::class, function ($mock) {
            $mock->shouldReceive('notifikasiPenugasan')->andReturn(true);
            $mock->shouldReceive('notifikasiPenuh')->andReturn(true);
        });
    }

    /** Helper: login sebagai user acak */
    private function loginUser()
    {
        $user = User::factory()->create();
        return $this->actingAs($user);
    }

    // ── Index ──────────────────────────────────────────
    public function test_user_bisa_melihat_daftar_tong()
    {
        Tong::factory()->count(3)->create();

        $response = $this->loginUser()->get('/daftar-tong');

        $response->assertStatus(200);
        $response->assertViewIs('daftar-tong');
        $response->assertViewHas('tongs');
    }

    // ── Store (tambah tong) ────────────────────────────
    public function test_user_bisa_menambah_tong_baru()
    {
        $response = $this->loginUser()->post('/daftar-tong', [
            'kode'      => 'tg001',
            'nama'      => 'Tong Gedung A',
            'lokasi'    => 'Lantai 1',
            'kapasitas' => 50,
        ]);

        $response->assertRedirect('/daftar-tong');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tongs', [
            'kode'      => 'TG001',
            'nama'      => 'Tong Gedung A',
            'persen'    => 0,
            'status'    => 'normal',
        ]);
    }

    public function test_gagal_tambah_tong_tanpa_kode()
    {
        $response = $this->loginUser()->post('/daftar-tong', [
            'nama'      => 'Tong Tanpa Kode',
            'kapasitas' => 50,
        ]);

        $response->assertSessionHasErrors('kode');
    }

    public function test_gagal_tambah_tong_dengan_kode_duplikat()
    {
        Tong::factory()->create(['kode' => 'TG001']);

        $response = $this->loginUser()->post('/daftar-tong', [
            'kode'      => 'TG001',
            'nama'      => 'Tong Duplikat',
            'kapasitas' => 50,
        ]);

        $response->assertSessionHasErrors('kode');
    }

    // ── Destroy (hapus tong) ───────────────────────────
    public function test_user_bisa_menghapus_tong()
    {
        $tong = Tong::factory()->create(['kode' => 'TG001']);
        Riwayat::factory()->create(['tong_id' => $tong->id]);
        Notifikasi::factory()->create(['tong_id' => $tong->id]);

        $response = $this->loginUser()->delete('/daftar-tong/TG001');

        $response->assertRedirect('/daftar-tong');
        $this->assertDatabaseMissing('tongs', ['kode' => 'TG001']);
        $this->assertDatabaseMissing('riwayats', ['tong_id' => $tong->id]);
        $this->assertDatabaseMissing('notifikasis', ['tong_id' => $tong->id]);
    }

    public function test_hapus_tong_yang_tidak_ada_menghasilkan_404()
    {
        $response = $this->loginUser()->delete('/daftar-tong/TIDAKADA');

        $response->assertStatus(404);
    }

    // ── Catat pengangkutan ─────────────────────────────
    public function test_user_bisa_mencatat_pengangkutan()
    {
        $tong = Tong::factory()->create([
            'kode'   => 'TG001',
            'persen' => 90,
            'status' => 'hampir_penuh',
        ]);

        $response = $this->loginUser()->post('/daftar-tong/TG001/angkut');

        $response->assertRedirect('/daftar-tong');

        $this->assertDatabaseHas('tongs', [
            'kode'   => 'TG001',
            'persen' => 0,
            'status' => 'normal',
        ]);

        $this->assertDatabaseHas('riwayats', [
            'tong_id' => $tong->id,
            'jenis'   => 'pengangkutan',
            'level'   => 90,
        ]);
    }

    // ── Sensor ESP32 (pengganti Postman kamu) ──────────
    // Catatan: route /api/sensor saat ini ada di dalam middleware 'auth',
    // jadi test ini sengaja login dulu supaya sesuai kondisi project sekarang.
    public function test_esp32_bisa_kirim_data_sensor_normal()
    {
        Tong::factory()->create(['kode' => 'TG001', 'persen' => 0]);

        $response = $this->loginUser()->postJson('/api/sensor', [
            'kode'   => 'TG001',
            'persen' => 45,
        ]);

        $response->assertStatus(200)
                  ->assertJson(['status' => 'ok']);

        $this->assertDatabaseHas('tongs', [
            'kode'   => 'TG001',
            'persen' => 45,
            'status' => 'normal',
        ]);
    }

    public function test_esp32_kirim_data_sensor_hampir_penuh()
    {
        Tong::factory()->create(['kode' => 'TG001']);

        $response = $this->loginUser()->postJson('/api/sensor', [
            'kode'   => 'TG001',
            'persen' => 85,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tongs', [
            'kode'   => 'TG001',
            'status' => 'hampir_penuh',
        ]);
    }

    public function test_esp32_kirim_data_sensor_penuh_membuat_notifikasi()
    {
        $tong = Tong::factory()->create(['kode' => 'TG001']);

        $response = $this->loginUser()->postJson('/api/sensor', [
            'kode'   => 'TG001',
            'persen' => 100,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tongs', [
            'kode'   => 'TG001',
            'status' => 'penuh',
        ]);

        $this->assertDatabaseHas('notifikasis', [
            'tong_id' => $tong->id,
            'tipe'    => 'penuh',
        ]);
    }

    public function test_esp32_kirim_kode_yang_tidak_ada_menghasilkan_404()
    {
        $response = $this->loginUser()->postJson('/api/sensor', [
            'kode'   => 'TIDAKADA',
            'persen' => 50,
        ]);

        $response->assertStatus(404)
                  ->assertJson(['error' => 'Tong tidak ditemukan']);
    }

    public function test_esp32_gagal_kirim_tanpa_kode()
    {
        $response = $this->loginUser()->postJson('/api/sensor', [
            'persen' => 50,
        ]);

        $response->assertStatus(422);
    }
}