@extends('layouts.app')
@section('title', 'Notifikasi')

@section('content')
<div class="p-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-[#e8f4eb] flex items-center justify-center">
                <svg class="w-6 h-6 text-[#22a846]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 01-3.46 0"/>
                </svg>
            </div>
            <div>
                <h1 class="text-[#0d2e1a] text-3xl font-extrabold">Notifikasi</h1>
                <p class="text-[#7a9a85] text-sm">Peringatan penting dari sensor tong sampah.</p>
            </div>
        </div>

        {{-- Jumlah total --}}
        @if($notifs->count() > 0)
        <div class="bg-white border border-[#e5ede5] rounded-xl px-4 py-2 text-sm text-[#7a9a85] shadow-sm">
            Total <span class="font-bold text-[#0d2e1a]">{{ $notifs->count() }}</span> notifikasi
        </div>
        @endif
    </div>

    @if($notifs->isEmpty())
        {{-- Empty state --}}
        <div class="bg-white rounded-2xl border border-[#e5ede5] shadow-sm p-16 text-center">
            <div class="w-16 h-16 bg-[#f2f6f0] rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-[#a8bfb0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 01-3.46 0"/>
                </svg>
            </div>
            <p class="text-[#0d2e1a] font-bold text-lg mb-1">Tidak ada notifikasi</p>
            <p class="text-[#7a9a85] text-sm">Semua tong dalam kondisi normal. Tidak ada peringatan saat ini.</p>
        </div>

    @else
        {{-- Notifikasi list --}}
        <div class="space-y-3 max-w-3xl">
            @foreach($notifs as $n)
            <div class="bg-white rounded-2xl border shadow-sm overflow-hidden
                {{ $n->tipe === 'penuh' ? 'border-red-200' : 'border-amber-200' }}">

                <div class="flex items-stretch">

                    {{-- Accent strip kiri --}}
                    <div class="w-1 flex-shrink-0 {{ $n->tipe === 'penuh' ? 'bg-red-500' : 'bg-amber-400' }}"></div>

                    {{-- Icon --}}
                    <div class="flex items-center justify-center px-4 py-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center
                            {{ $n->tipe === 'penuh' ? 'bg-red-50' : 'bg-amber-50' }}">
                            @if($n->tipe === 'penuh')
                                <svg class="w-5 h-5 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                                </svg>
                            @endif
                        </div>
                    </div>

                    {{-- Konten --}}
                    <div class="flex-1 py-4 pr-4">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                {{-- Judul --}}
                                <p class="font-bold text-sm {{ $n->tipe === 'penuh' ? 'text-red-700' : 'text-amber-700' }}">
                                    {{ $n->tipe === 'penuh' ? '🚨 Tong Penuh' : '⚠️ Hampir Penuh' }}
                                </p>
                                {{-- Nama & kode tong --}}
                                <p class="text-sm font-semibold text-[#0d2e1a] mt-0.5">
                                    {{ $n->tong->nama ?? 'Tong tidak diketahui' }}
                                    <span class="font-normal text-[#7a9a85] text-xs">
                                        ({{ $n->tong->kode ?? '–' }}
                                        @if($n->tong && $n->tong->lokasi) · {{ $n->tong->lokasi }} @endif)
                                    </span>
                                </p>
                                {{-- Pesan --}}
                                <p class="text-xs text-[#7a9a85] mt-1">{{ $n->pesan }}</p>
                            </div>
                            {{-- Waktu --}}
                            <div class="flex-shrink-0 text-right">
                                <p class="text-xs text-[#a8bfb0]">{{ $n->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-[#a8bfb0]">{{ $n->created_at->format('H:i') }}</p>
                                @if(!$n->sudah_dibaca)
                                    <span class="inline-block w-2 h-2 bg-red-500 rounded-full mt-1 ml-auto"></span>
                                @endif
                            </div>
                        </div>

                        {{-- Kapasitas tong saat ini --}}
                        @if($n->tong)
                        <div class="mt-3 flex items-center gap-2">
                            <div class="flex-1 bg-[#f2f6f0] rounded-full h-1.5 max-w-32">
                                <div class="h-1.5 rounded-full {{ $n->tong->persen >= 90 ? 'bg-red-500' : 'bg-amber-400' }}"
                                     style="width:{{ $n->tong->persen }}%"></div>
                            </div>
                            <span class="text-xs font-semibold text-[#0d2e1a]">{{ $n->tong->persen }}% saat ini</span>

                            {{-- Shortcut catat pengangkutan --}}
                            @if($n->tipe === 'penuh')
                            <form method="POST" action="{{ route('catat-pengangkutan', $n->tong->kode) }}" class="ml-2"
                                  onsubmit="return confirm('Catat pengangkutan {{ $n->tong->kode }}?')">
                                @csrf
                                <button type="submit"
                                        class="text-xs font-semibold text-[#22a846] border border-[#22a846]/30 hover:bg-[#f0faf4] px-2.5 py-1 rounded-lg transition"
                                        style="font-family:'Plus Jakarta Sans',sans-serif">
                                    Catat Angkut
                                </button>
                            </form>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Link ke riwayat --}}
        <div class="mt-6 max-w-3xl">
            <a href="{{ route('riwayat') }}"
               class="inline-flex items-center gap-2 text-sm text-[#22a846] font-semibold hover:underline">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                Lihat semua riwayat pengangkutan →
            </a>
        </div>
    @endif

</div>
@endsection
