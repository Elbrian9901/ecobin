@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="p-8 min-h-full">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-8">
        <div>
            <p class="text-[#7a9a85] text-sm mb-0.5">Selamat datang kembali 👋</p>
            <h1 class="text-[#0d2e1a] text-3xl font-extrabold">Dashboard Monitoring</h1>
        </div>
        <div class="text-right text-sm text-[#7a9a85] bg-white rounded-xl px-4 py-2 shadow-sm border border-[#e5ede5]"
             style="font-family:'Plus Jakarta Sans',sans-serif">
            <span id="live-clock"></span>
        </div>
    </div>

    {{-- 3 Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

        {{-- Total Tong --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e5ede5]">
            <div class="w-11 h-11 rounded-xl bg-[#e8f4eb] flex items-center justify-center mb-4">
                <svg viewBox="0 0 24 24" fill="none" stroke="#22a846" stroke-width="2" class="w-5 h-5">
                    <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/>
                    <path d="M10 11v6M14 11v6"/>
                </svg>
            </div>
            <p class="text-[#7a9a85] text-xs font-medium mb-1">Total Tong</p>
            <p class="text-[#0d2e1a] text-4xl font-extrabold">{{ $stats['total'] }}</p>
            <p class="text-[#a8bfb0] text-xs mt-1">Aktif terhubung</p>
        </div>

        {{-- Tong Penuh --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e5ede5]">
            <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center mb-4">
                <svg viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" class="w-5 h-5">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <p class="text-[#7a9a85] text-xs font-medium mb-1">Tong Penuh</p>
            <p class="text-[#0d2e1a] text-4xl font-extrabold">{{ $stats['penuh'] }}</p>
            <p class="text-[#a8bfb0] text-xs mt-1">Perlu diangkut</p>
        </div>

        {{-- Hampir Penuh --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e5ede5]">
            <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center mb-4">
                <svg viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" class="w-5 h-5">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                    <polyline points="17 6 23 6 23 12"/>
                </svg>
            </div>
            <p class="text-[#7a9a85] text-xs font-medium mb-1">Hampir Penuh</p>
            <p class="text-[#0d2e1a] text-4xl font-extrabold">{{ $stats['hampir_penuh'] }}</p>
            <p class="text-[#a8bfb0] text-xs mt-1">Status warning ≥ 80%</p>
        </div>

    </div>

    {{-- Chart --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e5ede5] mb-8">
        <div class="flex items-center justify-between mb-1">
            <div>
                <h3 class="font-bold text-[#0d2e1a] text-base">Kapasitas Tong Saat Ini</h3>
                <p class="text-[#a8bfb0] text-xs mt-0.5">Level real-time per tong, langsung dari sensor terakhir</p>
            </div>
            <span class="text-xs text-[#22a846] font-semibold bg-[#f0faf4] border border-[#b8dcbf] px-3 py-1.5 rounded-full">Live</span>
        </div>
        <canvas id="capacityChart" height="90" class="mt-5"></canvas>
    </div>

    {{-- Perlu Tindakan --}}
    <div class="bg-white rounded-2xl shadow-sm border border-[#e5ede5] p-6">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-4.5 h-4.5 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            <h3 class="font-bold text-[#0d2e1a] text-base">Perlu Tindakan</h3>
        </div>

        @forelse($tongsPerluTindakan as $tong)
        <div class="flex items-center justify-between gap-4 py-3 border-b border-[#f0f4f0] last:border-0">
            <div class="flex items-center gap-3 min-w-0">
                @if($tong->status === 'penuh')
                    <span class="text-xs font-semibold text-red-600 bg-red-50 px-2.5 py-1 rounded-full flex-shrink-0">● Penuh</span>
                @else
                    <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full flex-shrink-0">● Hampir Penuh</span>
                @endif
                <div class="min-w-0">
                    <p class="font-semibold text-[#0d2e1a] text-sm truncate">{{ $tong->kode }} &ndash; {{ $tong->nama }}</p>
                    <p class="text-[#7a9a85] text-xs truncate">{{ $tong->lokasi ?? 'Lokasi tidak diset' }} &middot; {{ $tong->persen }}% terisi</p>
                </div>
            </div>
            <form method="POST" action="{{ route('catat-pengangkutan', $tong->kode) }}" class="flex-shrink-0">
                @csrf
                <button type="submit"
                        class="text-xs font-semibold text-[#22a846] border border-[#22a846]/30 hover:bg-[#f0faf4] px-3 py-1.5 rounded-lg transition whitespace-nowrap"
                        onclick="return confirm('Catat pengangkutan {{ $tong->kode }}?')">
                    Catat Angkut
                </button>
            </form>
        </div>
        @empty
        <div class="flex items-center gap-2 py-2 text-sm text-[#22a846] font-medium">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M20 6L9 17l-5-5"/>
            </svg>
            Semua tong dalam kondisi normal.
        </div>
        @endforelse
    </div>



</div>
@endsection

@push('scripts')
<script>
function updateClock() {
    const now = new Date();
    const opt = { weekday:'long', year:'numeric', month:'short', day:'numeric', hour:'2-digit', minute:'2-digit' };
    document.getElementById('live-clock').textContent = 'Diperbarui ' + now.toLocaleString('id-ID', opt);
}
updateClock(); setInterval(updateClock, 60000);

// Chart.js — bar chart, kapasitas TERKINI per tong (0-100%), 1 batang = 1 tong
const ctx = document.getElementById('capacityChart').getContext('2d');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
            data: {!! json_encode($chartData) !!},
            backgroundColor: {!! json_encode($chartColors) !!},
            borderRadius: 8,
            maxBarThickness: 64
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#0d2e1a',
                padding: 10,
                cornerRadius: 8,
                titleFont: { size: 12, weight: 'bold' },
                bodyFont: { size: 11 },
                callbacks: {
                    label: function (item) { return 'Kapasitas: ' + item.parsed.y + '%'; }
                }
            }
        },
        scales: {
            y: {
                min: 0, max: 100,
                grid: { color: '#f0f4f0' },
                ticks: { color: '#a8bfb0', font: { size: 11 }, callback: v => v + '%' }
            },
            x: {
                grid: { display: false },
                ticks: { color: '#1a3d28', font: { size: 11, weight: '600' } }
            }
        }
    }
});
</script>
@endpush