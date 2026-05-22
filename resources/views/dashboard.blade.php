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
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-[#0d2e1a] text-base">Rata-rata Level Kapasitas (7 Hari Terakhir)</h3>
            <span class="text-xs text-[#a8bfb0] bg-[#f2f6f0] px-3 py-1 rounded-full">7 Hari</span>
        </div>
        <canvas id="capacityChart" height="100"></canvas>
    </div>

    {{-- Tabel status tong --}}
    <div class="bg-white rounded-2xl shadow-sm border border-[#e5ede5]">
        <div class="flex items-center justify-between p-6 border-b border-[#f0f4f0]">
            <h3 class="font-bold text-[#0d2e1a] text-base">Status Tong Saat Ini</h3>
            <a href="{{ route('daftar-tong') }}"
               class="text-xs text-[#22a846] font-semibold hover:underline">Kelola Tong →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-[#a8bfb0] text-xs font-semibold border-b border-[#f0f4f0]">
                        <th class="text-left px-6 py-3">KODE & NAMA</th>
                        <th class="text-left px-6 py-3">LOKASI</th>
                        <th class="text-left px-6 py-3">KAPASITAS</th>
                        <th class="text-left px-6 py-3">STATUS</th>
                        <th class="text-left px-6 py-3">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f5f8f3]">
                    @forelse($tongs as $tong)
                    <tr class="hover:bg-[#fafcfa] transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-[#0d2e1a] text-xs">{{ $tong->kode }}</p>
                            <p class="text-[#7a9a85] text-xs">{{ $tong->nama }}</p>
                        </td>
                        <td class="px-6 py-4 text-[#7a9a85] text-xs">{{ $tong->lokasi ?? '–' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-[#f2f6f0] rounded-full h-2 w-24">
                                    <div class="h-2 rounded-full {{ $tong->persen >= 90 ? 'bg-red-500' : ($tong->persen >= 70 ? 'bg-amber-400' : 'bg-[#22a846]') }}"
                                         style="width:{{ $tong->persen }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-[#0d2e1a]">{{ $tong->persen }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($tong->status === 'penuh')
                                <span class="text-xs font-semibold text-red-600 bg-red-50 px-2.5 py-1 rounded-full">● Penuh</span>
                            @elseif($tong->status === 'hampir_penuh')
                                <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">● Hampir Penuh</span>
                            @else
                                <span class="text-xs font-semibold text-green-600 bg-green-50 px-2.5 py-1 rounded-full">● Normal</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('catat-pengangkutan', $tong->kode) }}" class="inline">
                                @csrf
                                <button type="submit"
                                        class="text-xs font-semibold text-[#22a846] border border-[#22a846]/30 hover:bg-[#f0faf4] px-3 py-1.5 rounded-lg transition"
                                        style="font-family:'Plus Jakarta Sans',sans-serif"
                                        onclick="return confirm('Catat pengangkutan {{ $tong->kode }}?')">
                                    Catat Angkut
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-[#a8bfb0] text-sm">
                            Belum ada tong terdaftar.
                            <a href="{{ route('daftar-tong') }}" class="text-[#22a846] underline ml-1">Tambah sekarang</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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

// Chart.js
const ctx = document.getElementById('capacityChart').getContext('2d');
const grad = ctx.createLinearGradient(0,0,0,200);
grad.addColorStop(0,'rgba(34,168,70,0.25)');
grad.addColorStop(1,'rgba(34,168,70,0)');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
            data: {!! json_encode($chartData) !!},
            borderColor: '#22a846', borderWidth: 2.5,
            backgroundColor: grad, fill: true,
            tension: 0.45, pointRadius: 4,
            pointBackgroundColor: '#22a846',
            pointBorderColor: '#fff', pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { min:0, max:100, grid:{color:'#f0f4f0'}, ticks:{color:'#a8bfb0', font:{size:11}} },
            x: { grid:{display:false}, ticks:{color:'#a8bfb0', font:{size:11}} }
        }
    }
});
</script>
@endpush
