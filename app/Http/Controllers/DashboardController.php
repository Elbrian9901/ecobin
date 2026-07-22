<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Tong;
use App\Models\Riwayat;
use App\Models\Notifikasi;
class DashboardController extends Controller
{
    // ── Dashboard utama ─────────────────────────────────────────
    public function index()
    {
        $stats = [
            'total'        => Tong::count(),
            'penuh'        => Tong::where('status', 'penuh')->count(),
            'hampir_penuh' => Tong::where('status', 'hampir_penuh')->count(),
        ];

        $tongs = Tong::orderBy('kode')->get();

        // Data bar chart: kapasitas TERKINI per tong, langsung dari kolom `persen`
        // (kolom ini selalu ter-update real-time oleh MQTT subscriber saat ESP32 kirim data,
        // jadi lebih akurat daripada dihitung ulang/dirata-rata dari histori)
        $chartLabels = [];
        $chartData   = [];
        $chartColors = [];
        foreach ($tongs as $tong) {
            $chartLabels[] = $tong->kode . ' – ' . $tong->nama;
            $chartData[]   = $tong->persen;
            $chartColors[] = $tong->persen >= 90 ? '#ef4444' : ($tong->persen >= 70 ? '#f59e0b' : '#22a846');
        }

        // Tong yang butuh tindakan segera (untuk section "Perlu Tindakan" di dashboard)
        $tongsPerluTindakan = $tongs->whereIn('status', ['penuh', 'hampir_penuh'])
            ->sortByDesc('persen')
            ->values();

        return view('dashboard', compact('stats', 'tongs', 'chartLabels', 'chartData', 'chartColors', 'tongsPerluTindakan'));
    }

    // ── Riwayat ──────────────────────────────────────────────────
    // Hanya menampilkan riwayat jenis "pengangkutan".
    // Setiap baris bisa diklik untuk melihat detail (termasuk nomor
    // WhatsApp penanggung jawab tong terkait) di halaman riwayat.blade.php
    public function riwayat(Request $request)
    {
        $query = Riwayat::with('tong')
            ->where('jenis', 'pengangkutan')
            ->latest('waktu');

        if ($request->q) {
            $q = $request->q;
            $query->whereHas('tong', function ($tq) use ($q) {
                $tq->where('nama', 'like', "%$q%")
                   ->orWhere('kode', 'like', "%$q%")
                   ->orWhere('lokasi', 'like', "%$q%");
            });
        }

        $riwayat = $query->paginate(20)->withQueryString();
        return view('riwayat', compact('riwayat'));
    }

    // ── Notifikasi ───────────────────────────────────────────────
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