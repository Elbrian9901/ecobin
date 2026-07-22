<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Riwayat;
use App\Models\Tong;

class LaporanController extends Controller
{
    // Batas waktu (jam) sebelum sebuah tong Penuh dianggap "Terlambat" diangkut.
    // Ubah angka ini kalau dosen minta standar SLA yang berbeda.
    private const BATAS_TERLAMBAT_JAM = 1;

    public function index()
    {
        $tongs = Tong::orderBy('kode')->get();
        return view('laporan', compact('tongs'));
    }

    public function download(Request $request)
    {
        $request->validate([
            'dari'        => ['required', 'date'],
            'sampai'      => ['required', 'date', 'after_or_equal:dari'],
            'tong_id'     => ['required', 'string'],
            'keterangan'  => ['nullable', 'string', 'max:500'],
        ], [
            'dari.required'    => 'Tanggal mulai wajib diisi.',
            'sampai.required'  => 'Tanggal akhir wajib diisi.',
            'tong_id.required' => 'Pilih tong sampah terlebih dahulu.',
            'keterangan.max'   => 'Keterangan maksimal 500 karakter.',
        ]);

        // Kalau keterangan kosong/tidak diisi, default jadi "-"
        $keterangan = trim((string) $request->keterangan) !== ''
            ? trim($request->keterangan)
            : '-';

        $tongsQuery = Tong::orderBy('kode');
        if ($request->tong_id !== 'semua') {
            $tongsQuery->where('kode', $request->tong_id);
        }
        $tongs = $tongsQuery->get();

        $laporanPerTong = [];

        foreach ($tongs as $tong) {
            $riwayat = Riwayat::where('tong_id', $tong->id)
                ->whereDate('waktu', '>=', $request->dari)
                ->whereDate('waktu', '<=', $request->sampai)
                ->orderBy('waktu')
                ->get();

            // ── Riwayat sensor: tampilkan 5 PEMBACAAN TERAKHIR (data asli/raw) ──
            // supaya laporan tidak kepanjangan dan tetap akurat (tidak dirata-rata)
            $riwayatSensor = $riwayat->where('jenis', 'sensor')
                ->sortByDesc('waktu')   // urutkan dari pembacaan paling baru
                ->take(5)               // ambil 5 pembacaan terakhir saja
                ->sortBy('waktu')       // urutkan lagi kronologis (lama -> baru) untuk ditampilkan
                ->values();

            // ── Hitung siklus: dari saat Penuh sampai diangkut ──
            $siklus = [];
            $waktuPenuh = null;

            foreach ($riwayat as $r) {
                if ($r->jenis === 'sensor' && $r->level >= 100 && !$waktuPenuh) {
                    $waktuPenuh = $r->waktu;
                }

                if ($r->jenis === 'pengangkutan') {
                    if ($waktuPenuh) {
                        $durasiJam = $waktuPenuh->diffInMinutes($r->waktu) / 60;
                        $siklus[] = [
                            'waktu_penuh'  => $waktuPenuh,
                            'waktu_angkut' => $r->waktu,
                            'durasi_jam'   => round($durasiJam, 1),
                            'status'       => $durasiJam <= self::BATAS_TERLAMBAT_JAM ? 'Tepat Waktu' : 'Terlambat',
                        ];
                        $waktuPenuh = null;
                    } else {
                        // Pengangkutan dicatat walau tong belum sempat sensor mendeteksi "penuh"
                        // (misal pengangkutan rutin/preventif)
                        $siklus[] = [
                            'waktu_penuh'  => null,
                            'waktu_angkut' => $r->waktu,
                            'durasi_jam'   => null,
                            'status'       => 'Pengangkutan Rutin',
                        ];
                    }
                }
            }

            // Kalau sampai akhir periode masih Penuh dan belum diangkut
            if ($waktuPenuh) {
                $siklus[] = [
                    'waktu_penuh'  => $waktuPenuh,
                    'waktu_angkut' => null,
                    'durasi_jam'   => null,
                    'status'       => 'Belum Diangkut',
                ];
            }

            // Statistik dihitung dari SELURUH siklus pada periode (biar ringkasan tetap akurat)
            $totalTepatWaktu    = collect($siklus)->where('status', 'Tepat Waktu')->count();
            $totalTerlambat     = collect($siklus)->where('status', 'Terlambat')->count();
            $totalBelumDiangkut = collect($siklus)->where('status', 'Belum Diangkut')->count();
            $rataRataDurasi     = collect($siklus)->pluck('durasi_jam')->filter()->avg();

            // Tapi yang DITAMPILKAN di tabel cukup 1 siklus terakhir saja
            $siklus = collect($siklus)->sortByDesc(fn ($s) => $s['waktu_angkut'] ?? $s['waktu_penuh'])
                ->take(1)
                ->values()
                ->all();

            $laporanPerTong[] = [
                'tong'                  => $tong,
                'riwayat_sensor'        => $riwayatSensor,
                'siklus'                => $siklus,
                'total_tepat_waktu'     => $totalTepatWaktu,
                'total_terlambat'       => $totalTerlambat,
                'total_belum_diangkut'  => $totalBelumDiangkut,
                'rata_rata_durasi'      => $rataRataDurasi ? round($rataRataDurasi, 1) : null,
            ];
        }

        $tongInfo = $request->tong_id === 'semua'
            ? 'Semua Tong'
            : (optional($tongs->first())->nama
                ? $tongs->first()->nama . ' (' . $tongs->first()->kode . ')'
                : $request->tong_id);

        $tongSlug = $request->tong_id === 'semua' ? 'semua-tong' : strtolower($request->tong_id);
        $namaFile = 'ecobin_laporan_' . $tongSlug . '_' . $request->dari . '_sd_' . $request->sampai . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan-pdf', [
            'laporanPerTong'      => $laporanPerTong,
            'dari'                => $request->dari,
            'sampai'              => $request->sampai,
            'tongInfo'            => $tongInfo,
            'batasTerlambatJam'   => self::BATAS_TERLAMBAT_JAM,
            'keterangan'          => $keterangan,
        ])->setPaper('a4', 'portrait');

        return $pdf->download($namaFile);
    }
}