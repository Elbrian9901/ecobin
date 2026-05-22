@extends('layouts.app')
@section('title', 'Riwayat')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <h1 class="text-[#0d2e1a] text-3xl font-extrabold">Riwayat Pengangkutan</h1>
        <p class="text-[#7a9a85] text-sm mt-1">Catatan seluruh pengangkutan dan data sensor tong sampah.</p>
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
        <select name="jenis"
                onchange="this.form.submit()"
                class="px-4 py-2.5 rounded-xl border border-[#e0e8dc] bg-white text-sm text-[#1a2820] outline-none focus:border-[#22a846] transition"
                style="font-family:'Plus Jakarta Sans',sans-serif">
            <option value="">Semua Jenis</option>
            <option value="pengangkutan" {{ request('jenis')==='pengangkutan' ? 'selected':'' }}>Pengangkutan</option>
            <option value="tong_penuh"   {{ request('jenis')==='tong_penuh'   ? 'selected':'' }}>Tong Penuh</option>
            <option value="sensor"       {{ request('jenis')==='sensor'       ? 'selected':'' }}>Data Sensor</option>
        </select>
        @if(request('q') || request('jenis'))
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
                <p class="font-medium text-sm">Belum ada riwayat</p>
                <p class="text-xs mt-1">Riwayat akan muncul setelah pengangkutan dicatat.</p>
            </div>
        @else
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-[#f8fbf8] border-b border-[#f0f4f0]">
                    <th class="text-left px-6 py-3 text-[#a8bfb0] font-semibold text-xs">JENIS</th>
                    <th class="text-left px-6 py-3 text-[#a8bfb0] font-semibold text-xs">TONG</th>
                    <th class="text-left px-6 py-3 text-[#a8bfb0] font-semibold text-xs">LEVEL</th>
                    <th class="text-left px-6 py-3 text-[#a8bfb0] font-semibold text-xs">WAKTU</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f5f8f3]">
                @foreach($riwayat as $r)
                <tr class="hover:bg-[#fafcfa] transition-colors">
                    <td class="px-6 py-4">
                        @if($r->jenis === 'pengangkutan')
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">
                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 7h14l1 8H4L5 7z"/><path d="M8 7V5a4 4 0 018 0v2"/></svg>
                                Pengangkutan
                            </span>
                        @elseif($r->jenis === 'tong_penuh')
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-600 bg-red-50 px-2.5 py-1 rounded-full">
                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                Tong Penuh
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">
                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/></svg>
                                Data Sensor
                            </span>
                        @endif
                    </td>
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
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($riwayat->hasPages())
        <div class="px-6 py-4 border-t border-[#f0f4f0]">
            {{ $riwayat->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
