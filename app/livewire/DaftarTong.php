<?php

namespace App\Livewire;

use App\Models\Tong;
use App\Models\Riwayat;
use App\Models\Notifikasi;
use App\Services\WhatsappService;
use Livewire\Component;

class DaftarTong extends Component
{
    protected WhatsappService $wa;

    // ==== State modal tambah tong ====
    public bool $showModal = false;

    // ==== Form fields ====
    public string $kode = '';
    public string $nama = '';
    public string $lokasi = '';
    public string $no_whatsapp = '';
    public int $kapasitas = 60;

    // Interval polling (detik) — realtime dari data sensor ESP32
    public int $pollInterval = 5;

    /**
     * Livewire otomatis resolve dependency ini dari service container
     * setiap kali komponen di-boot (initial load maupun tiap update/poll).
     */
    public function boot(WhatsappService $wa): void
    {
        $this->wa = $wa;
    }

    protected function rules(): array
    {
        return [
            'kode'        => ['required', 'string', 'max:20', 'unique:tongs,kode'],
            'nama'        => ['required', 'string', 'max:100'],
            'lokasi'      => ['nullable', 'string', 'max:150'],
            'no_whatsapp' => ['nullable', 'string', 'max:20'],
            'kapasitas'   => ['required', 'integer', 'min:1'],
        ];
    }

    protected $messages = [
        'kode.unique'         => 'Kode tong sudah digunakan.',
        'kode.required'       => 'Kode tong wajib diisi.',
        'nama.required'       => 'Nama tong wajib diisi.',
        'kapasitas.required'  => 'Kapasitas wajib diisi.',
    ];

    public function render()
    {
        return view('livewire.daftar-tong', [
            'tongs'        => Tong::orderBy('kode')->get(),
            'pollInterval' => $this->pollInterval,
        ]);
    }

    public function bukaModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function tutupModal(): void
    {
        $this->showModal = false;
    }

    // ── Tambah tong baru ──────────────────────────────────────────
    public function simpanTong(): void
    {
        $data = $this->validate();

        $tong = Tong::create([
            'kode'        => strtoupper($data['kode']),
            'nama'        => $data['nama'],
            'lokasi'      => $data['lokasi'],
            'no_whatsapp' => $data['no_whatsapp'],
            'kapasitas'   => $data['kapasitas'],
            'persen'      => 0,
            'status'      => 'normal',
        ]);

        // Notifikasi 1 - Kirim WA penugasan penanggung jawab
        if ($tong->no_whatsapp) {
            $this->wa->notifikasiPenugasan($tong);
        }

        $this->tutupModal();
        session()->flash('success', 'Tong ' . $tong->kode . ' berhasil ditambahkan.');
    }

    // ── Hapus tong ────────────────────────────────────────────────
    public function hapusTong(string $kode): void
    {
        $tong = Tong::where('kode', $kode)->firstOrFail();

        Riwayat::where('tong_id', $tong->id)->delete();
        Notifikasi::where('tong_id', $tong->id)->delete();
        $tong->delete();

        session()->flash('success', 'Tong ' . $kode . ' berhasil dihapus.');
    }

    // ── Catat pengangkutan ────────────────────────────────────────
    public function catatPengangkutan(string $kode): void
    {
        $tong = Tong::where('kode', $kode)->firstOrFail();

        Riwayat::create([
            'tong_id' => $tong->id,
            'jenis'   => 'pengangkutan',
            'level'   => $tong->persen,
            'waktu'   => now(),
        ]);

        $tong->update([
            'persen' => 0,
            'status' => 'normal',
        ]);

        session()->flash('success', 'Pengangkutan tong ' . $kode . ' berhasil dicatat. Kapasitas direset ke 0%.');
    }

    private function resetForm(): void
    {
        $this->reset(['kode', 'nama', 'lokasi', 'no_whatsapp']);
        $this->kapasitas = 60;
        $this->resetErrorBag();
        $this->resetValidation();
    }
}