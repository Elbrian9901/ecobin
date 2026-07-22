@extends('layouts.app')
@section('title', 'Riwayat')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <h1 class="text-[#0d2e1a] text-3xl font-extrabold">Riwayat Pengangkutan</h1>
        <p class="text-[#7a9a85] text-sm mt-1">Catatan seluruh pengangkutan tong sampah.</p>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('riwayat') }}" class="flex gap-3 mb-6">
        <div class="flex-1 relative">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-[#a8bfb0]"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Cari kode / nama / lokasi tong..."
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#e0e8dc] bg-white text-sm text-[#1a2820] outline-none focus:border-[#22a846] focus:ring-2 focus:ring-[#22a846]/10 transition"
                   style="font-family:'Plus Jakarta Sans',sans-serif">
        </div>
        @if(request('q'))
            <a href="{{ route('riwayat') }}"
               class="flex items-center px-4 py-2.5 rounded-xl border border-[#e0e8dc] bg-white text-sm text-[#7a9a85] hover:text-[#0d2e1a] transition"
               style="font-family:'Plus Jakarta Sans',sans-serif">
                Reset
            </a>
        @endif
    </form>

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl shadow-sm border border-[#e5ede5] overflow-hidden">
        @if($riwayat->isEmpty())
            <div class="p-16 text-center text-[#a8bfb0]">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                <p class="font-medium text-sm">Belum ada riwayat pengangkutan</p>
                <p class="text-xs mt-1">Riwayat akan muncul setelah pengangkutan dicatat.</p>
            </div>
        @else
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-[#f8fbf8] border-b border-[#f0f4f0]">
                    <th class="text-left px-6 py-3 text-[#a8bfb0] font-semibold text-xs">TONG</th>
                    <th class="text-left px-6 py-3 text-[#a8bfb0] font-semibold text-xs">LEVEL SAAT DIANGKUT</th>
                    <th class="text-left px-6 py-3 text-[#a8bfb0] font-semibold text-xs">WAKTU</th>
                    <th class="text-left px-6 py-3 text-[#a8bfb0] font-semibold text-xs">DETAIL</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f5f8f3]">
                @foreach($riwayat as $r)
                <tr class="hover:bg-[#fafcfa] transition-colors cursor-pointer"
                    onclick="bukaDetail({{ $r->id }})">
                    <td class="px-6 py-4">
                        <p class="font-semibold text-[#0d2e1a]">{{ $r->tong->nama ?? '–' }}</p>
                        <p class="text-[#a8bfb0] text-xs">
                            {{ $r->tong->kode ?? '–' }}
                            @if($r->tong && $r->tong->lokasi) · {{ $r->tong->lokasi }} @endif
                        </p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-16 bg-[#f2f6f0] rounded-full h-1.5">
                                <div class="h-1.5 rounded-full {{ $r->level >= 90 ? 'bg-red-400' : ($r->level >= 70 ? 'bg-amber-400' : 'bg-[#22a846]') }}"
                                     style="width:{{ $r->level }}%"></div>
                            </div>
                            <span class="text-xs font-semibold text-[#0d2e1a]">{{ $r->level }}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-[#a8bfb0] text-xs">
                        {{ \Carbon\Carbon::parse($r->waktu)->format('d M Y, H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        <button type="button" class="text-[#22a846] text-xs font-semibold hover:underline">
                            Lihat Detail
                        </button>
                    </td>
                </tr>

                {{-- Data tersembunyi untuk modal --}}
                <tr class="hidden" id="data-riwayat-{{ $r->id }}"
                    data-nama="{{ $r->tong->nama ?? '–' }}"
                    data-kode="{{ $r->tong->kode ?? '–' }}"
                    data-lokasi="{{ $r->tong->lokasi ?? '–' }}"
                    data-level="{{ $r->level }}%"
                    data-waktu="{{ \Carbon\Carbon::parse($r->waktu)->format('d M Y, H:i') }}"
                    data-wa="{{ $r->tong->no_whatsapp ?? '' }}">
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($riwayat->hasPages())
        <div class="px-6 py-4 border-t border-[#f0f4f0]">
            {{ $riwayat->links() }}
        </div>
        @endif
        @endif
    </div>
</div>

{{-- Modal Detail --}}
<div id="modal-detail" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 relative">
        <button onclick="tutupDetail()" class="absolute top-4 right-4 text-[#a8bfb0] hover:text-[#0d2e1a]">✕</button>
        <h2 class="text-xl font-bold text-[#0d2e1a] mb-4">Detail Pengangkutan</h2>

        <div class="space-y-3 text-sm">
            <div>
                <p class="text-[#a8bfb0] text-xs">Tong</p>
                <p id="d-nama" class="font-semibold text-[#0d2e1a]"></p>
                <p id="d-kode-lokasi" class="text-[#7a9a85] text-xs"></p>
            </div>
            <div>
                <p class="text-[#a8bfb0] text-xs">Level Kapasitas Saat Diangkut</p>
                <p id="d-level" class="font-semibold text-[#0d2e1a]"></p>
            </div>
            <div>
                <p class="text-[#a8bfb0] text-xs">Waktu Pengangkutan</p>
                <p id="d-waktu" class="font-semibold text-[#0d2e1a]"></p>
            </div>
            <div>
                <p class="text-[#a8bfb0] text-xs">Penanggung Jawab (No. WhatsApp)</p>
                <p id="d-wa" class="font-semibold text-[#0d2e1a]"></p>
            </div>
        </div>
    </div>
</div>

<script>
function bukaDetail(id) {
    const src = document.getElementById('data-riwayat-' + id);
    if (!src) return;

    document.getElementById('d-nama').innerText = src.dataset.nama;
    document.getElementById('d-kode-lokasi').innerText = src.dataset.kode + ' · ' + src.dataset.lokasi;
    document.getElementById('d-level').innerText = src.dataset.level;
    document.getElementById('d-waktu').innerText = src.dataset.waktu;
    document.getElementById('d-wa').innerText = src.dataset.wa || 'Belum diatur';

    document.getElementById('modal-detail').classList.remove('hidden');
}

function tutupDetail() {
    document.getElementById('modal-detail').classList.add('hidden');
}
</script>
@endsection