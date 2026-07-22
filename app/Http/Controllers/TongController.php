<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tong;
use App\Models\Riwayat;
use App\Models\Notifikasi;
use App\Services\WhatsappService;

class TongController extends Controller
{
    protected WhatsappService $wa;

    public function __construct(WhatsappService $wa)
    {
        $this->wa = $wa;
    }

    // ── Daftar semua tong ──────────────────────────────────────
    public function index()
    {
        $tongs = Tong::orderBy('kode')->get();
        return view('daftar-tong', compact('tongs'));
    }

    // ── Tambah tong baru ───────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'kode'        => ['required', 'string', 'max:20', 'unique:tongs,kode'],
            'nama'        => ['required', 'string', 'max:100'],
            'lokasi'      => ['nullable', 'string', 'max:150'],
            'no_whatsapp' => ['nullable', 'string', 'regex:/^(08[0-9]{9,11}|\+62[0-9]{9,11})$/'],
            'kapasitas'   => ['required', 'integer', 'min:1'],
        ], [
            'kode.unique'        => 'Kode tong sudah digunakan.',
            'kode.required'      => 'Kode tong wajib diisi.',
            'nama.required'      => 'Nama tong wajib diisi.',
            'kapasitas.required' => 'Kapasitas wajib diisi.',
            'no_whatsapp.regex'  => 'Nomor WhatsApp harus diawali 08 atau +62, contoh: 081234567890.',
        ]);

        $tong = Tong::create([
            'kode'        => strtoupper($request->kode),
            'nama'        => $request->nama,
            'lokasi'      => $request->lokasi,
            'no_whatsapp' => $request->no_whatsapp,
            'kapasitas'   => $request->kapasitas,
            'persen'      => 0,
            'status'      => 'normal',
        ]);

        // Notifikasi 1 - Kirim WA penugasan penanggung jawab
        if ($tong->no_whatsapp) {
            $this->wa->notifikasiPenugasan($tong);
        }

        return redirect()->route('daftar-tong')
            ->with('success', 'Tong ' . strtoupper($request->kode) . ' berhasil ditambahkan.');
    }

    // ── Hapus tong ──────────────────────────────────────────────
    public function destroy($kode)
    {
        $tong = Tong::where('kode', $kode)->firstOrFail();

        Riwayat::where('tong_id', $tong->id)->delete();
        Notifikasi::where('tong_id', $tong->id)->delete();
        $tong->delete();

        return redirect()->route('daftar-tong')
            ->with('success', 'Tong ' . $kode . ' berhasil dihapus.');
    }

    // ── Catat pengangkutan ──────────────────────────────────────
    public function catat($kode)
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

        return redirect()->route('daftar-tong')
            ->with('success', 'Pengangkutan tong ' . $kode . ' berhasil dicatat. Kapasitas direset ke 0%.');
    }

    // ── Terima data sensor ESP32 (HTTP POST) ─────────────────────
    public function receiveSensor(Request $request)
    {
        $request->validate([
            'kode'   => ['required', 'string'],
            'persen' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $tong = Tong::where('kode', $request->kode)->first();

        if (!$tong) {
            return response()->json(['error' => 'Tong tidak ditemukan'], 404);
        }

        $status = 'normal';
        if ($request->persen >= 100)    $status = 'penuh';
        elseif ($request->persen >= 80) $status = 'hampir_penuh';

        $tong->update([
            'persen' => $request->persen,
            'status' => $status,
        ]);

        Riwayat::create([
            'tong_id' => $tong->id,
            'jenis'   => 'sensor',
            'level'   => $request->persen,
            'waktu'   => now(),
        ]);

        // Notifikasi 2 - Kirim WA ketika tong penuh
        if ($status === 'penuh') {
            Notifikasi::create([
                'tong_id' => $tong->id,
                'tipe'    => 'penuh',
                'pesan'   => 'Tong mencapai kapasitas penuh',
            ]);

            if ($tong->no_whatsapp) {
                $this->wa->notifikasiPenuh($tong);
            }
        }

        return response()->json(['status' => 'ok', 'message' => 'Data sensor diterima']);
    }
}