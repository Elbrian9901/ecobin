@extends('layouts.app')
@section('title', 'Laporan PDF')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <h1 class="text-[#0d2e1a] text-3xl font-extrabold">Laporan PDF</h1>
        <p class="text-[#7a9a85] text-sm mt-1">Unduh laporan riwayat &amp; keterlambatan pengangkutan tong sampah dalam format PDF.</p>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-red-800 mb-1">Lengkapi form berikut:</p>
                <ul class="text-sm text-red-700 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>&bull; {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    @if(session('info'))
    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-2xl p-4 text-sm text-blue-800">
        {{ session('info') }}
    </div>
    @endif

    {{-- Unduh Cepat per Tong (Laporan Otomatis) --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <div class="xl:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-[#e5ede5] p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-[#f0faf4] flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#22a846]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-[#0d2e1a]">Unduh Laporan per Tong</h2>
                        <p class="text-xs text-[#7a9a85]">Laporan otomatis 7 hari terakhir. Isi keterangan (opsional) sebelum diunduh.</p>
                    </div>
                </div>

                @forelse($tongs as $tong)
                <div class="py-4 border-b border-[#f0f4f0] last:border-0">
                    <form method="GET" action="{{ route('laporan.download') }}" class="space-y-2.5">
                        <input type="hidden" name="dari" value="{{ now()->subDays(6)->format('Y-m-d') }}">
                        <input type="hidden" name="sampai" value="{{ now()->format('Y-m-d') }}">
                        <input type="hidden" name="tong_id" value="{{ $tong->kode }}">

                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="text-xs font-bold px-2 py-1 rounded-full flex-shrink-0
                                    {{ $tong->status === 'penuh' ? 'bg-red-100 text-red-700' : ($tong->status === 'hampir_penuh' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }}">
                                    {{ $tong->persen }}%
                                </span>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-[#0d2e1a] truncate">{{ $tong->kode }} &ndash; {{ $tong->nama }}</p>
                                    <p class="text-xs text-[#7a9a85] truncate">{{ $tong->lokasi ?? 'Lokasi tidak diset' }}</p>
                                </div>
                            </div>
                            <button type="submit"
                                    class="flex items-center gap-1.5 text-xs font-semibold text-[#22a846] border border-[#22a846]/30 hover:bg-[#f0faf4] px-3 py-2 rounded-xl transition whitespace-nowrap flex-shrink-0">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                Unduh
                            </button>
                        </div>

                        <input type="text" name="keterangan" maxlength="500"
                               placeholder="Keterangan (opsional, kosongkan jika tidak ada &mdash; akan diisi &quot;-&quot;)"
                               class="w-full px-3 py-2 rounded-lg border border-[#e0e8dc] bg-[#f8fbf8] text-xs outline-none focus:border-[#22a846] focus:ring-2 focus:ring-[#22a846]/10 transition">
                    </form>
                </div>
                @empty
                <p class="text-sm text-[#7a9a85]">Belum ada tong terdaftar. Tambahkan tong dulu di menu Daftar Tong.</p>
                @endforelse

                @if($tongs->count() > 1)
                <div class="pt-4 mt-1 border-t border-[#f0f4f0]">
                    <form method="GET" action="{{ route('laporan.download') }}" class="space-y-2.5">
                        <input type="hidden" name="dari" value="{{ now()->subDays(6)->format('Y-m-d') }}">
                        <input type="hidden" name="sampai" value="{{ now()->format('Y-m-d') }}">
                        <input type="hidden" name="tong_id" value="semua">

                        <input type="text" name="keterangan" maxlength="500"
                               placeholder="Keterangan (opsional, kosongkan jika tidak ada &mdash; akan diisi &quot;-&quot;)"
                               class="w-full px-3 py-2 rounded-lg border border-[#e0e8dc] bg-[#f8fbf8] text-xs outline-none focus:border-[#22a846] focus:ring-2 focus:ring-[#22a846]/10 transition">

                        <button type="submit"
                                class="flex items-center justify-center gap-2 w-full bg-[#22a846] hover:bg-[#1a8c38] text-white text-sm font-bold px-5 py-3 rounded-xl transition">
                            Unduh Laporan Semua Tong (7 Hari Terakhir)
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-4">

            {{-- Ringkasan cepat --}}
            <div class="bg-white rounded-2xl shadow-sm border border-[#e5ede5] p-5">
                <h3 class="font-bold text-[#0d2e1a] text-sm mb-4">Ringkasan Sistem</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-[#7a9a85]">Total Tong</span>
                        <span class="font-bold text-[#0d2e1a]">{{ \App\Models\Tong::count() }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-[#7a9a85]">Total Pengangkutan</span>
                        <span class="font-bold text-[#0d2e1a]">{{ \App\Models\Riwayat::where('jenis','pengangkutan')->count() }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-[#7a9a85]">Tong Penuh Sekarang</span>
                        <span class="font-bold text-red-500">{{ \App\Models\Tong::where('status','penuh')->count() }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-[#7a9a85]">Notifikasi Belum Dibaca</span>
                        <span class="font-bold text-amber-500">{{ \App\Models\Notifikasi::where('sudah_dibaca',false)->count() }}</span>
                    </div>
                </div>
            </div>

            {{-- Panduan --}}
            <div class="bg-[#f0faf4] border border-[#b3ddbf] rounded-2xl p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-[#22a846] flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-[#1a5c36] mb-1">Cara menggunakan</p>
                        <p class="text-xs text-[#1a5c36] leading-relaxed">
                            Pilih tong, isi keterangan kalau perlu (opsional), lalu klik <strong>Unduh</strong>.
                            Laporan otomatis mencakup periode 7 hari terakhir dari hari ini.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection