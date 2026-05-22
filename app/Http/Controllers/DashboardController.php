<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tong;
use App\Models\Riwayat;
use App\Models\Notifikasi;

class DashboardController extends Controller
{
    // ── Dashboard utama ───────────────────────────────────────────
    public function index()
    {
        $stats = [
            'total'        => Tong::count(),
            'penuh'        => Tong::where('status', 'penuh')->count(),
            'hampir_penuh' => Tong::where('status', 'hampir_penuh')->count(),
        ];

        // Data chart 7 hari terakhir (rata-rata level per hari)
        $chartData = [];
        $chartLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('d M');
            $avg = Riwayat::whereDate('waktu', $date->toDateString())
                ->where('jenis', 'sensor')
                ->avg('level');
            $chartData[] = round($avg ?? 0, 1);
        }

        $tongs = Tong::orderBy('kode')->get();

        return view('dashboard', compact('stats', 'chartData', 'chartLabels', 'tongs'));
    }

    // ── Riwayat ───────────────────────────────────────────────────
    public function riwayat(Request $request)
    {
        $query = Riwayat::with('tong')->latest('waktu');

        if ($request->q) {
            $q = $request->q;
            $query->whereHas('tong', function($tq) use ($q) {
                $tq->where('nama', 'like', "%$q%")
                   ->orWhere('kode', 'like', "%$q%")
                   ->orWhere('lokasi', 'like', "%$q%");
            });
        }

        if ($request->jenis) {
            $query->where('jenis', $request->jenis);
        }

        $riwayat = $query->paginate(20)->withQueryString();

        return view('riwayat', compact('riwayat'));
    }

    // ── Notifikasi ────────────────────────────────────────────────
    public function notifikasi()
    {
        $notifs = Notifikasi::with('tong')->latest()->get();

        // Tandai semua sebagai sudah dibaca
        Notifikasi::where('sudah_dibaca', false)->update(['sudah_dibaca' => true]);

        return view('notifikasi', compact('notifs'));
    }

    // ── Hitung notifikasi belum dibaca (untuk badge sidebar) ──────
    public static function unreadCount()
    {
        return Notifikasi::where('sudah_dibaca', false)->count();
    }
}
