@extends('layouts.app')
@section('title', 'Laporan PDF')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <h1 class="text-[#0d2e1a] text-3xl font-extrabold">Laporan PDF</h1>
        <p class="text-[#7a9a85] text-sm mt-1">Unduh laporan pengangkutan tong sampah dalam format PDF.</p>
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
                        <li>• {{ $error }}</li>
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

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Form download --}}
        <div class="xl:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-[#e5ede5] p-6">
                <div class="flex items-center gap-3 mb-6 pb-5 border-b border-[#f0f4f0]">
                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-[#0d2e1a]">Generate Laporan</h2>
                        <p class="text-xs text-[#7a9a85]">Semua field wajib diisi sebelum mengunduh PDF</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('laporan.download') }}" class="space-y-5">

                    {{-- Rentang Tanggal --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-[#1a3d28] mb-1.5">
                                Tanggal Mulai <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="dari" required
                                   value="{{ request('dari') }}"
                                   class="w-full px-4 py-3 rounded-xl border {{ $errors->has('dari') ? 'border-red-400 bg-red-50' : 'border-[#e0e8dc] bg-[#f8fbf8]' }} text-sm outline-none focus:border-[#22a846] focus:ring-2 focus:ring-[#22a846]/10 transition">
                            @error('dari')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#1a3d28] mb-1.5">
                                Tanggal Akhir <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="sampai" required
                                   value="{{ request('sampai') }}"
                                   class="w-full px-4 py-3 rounded-xl border {{ $errors->has('sampai') ? 'border-red-400 bg-red-50' : 'border-[#e0e8dc] bg-[#f8fbf8]' }} text-sm outline-none focus:border-[#22a846] focus:ring-2 focus:ring-[#22a846]/10 transition">
                            @error('sampai')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Pilih Tong --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#1a3d28] mb-1.5">
                            Tong Sampah <span class="text-red-500">*</span>
                        </label>
                        <select name="tong_id" required
                                class="w-full px-4 py-3 rounded-xl border {{ $errors->has('tong_id') ? 'border-red-400 bg-red-50' : 'border-[#e0e8dc] bg-[#f8fbf8]' }} text-sm outline-none focus:border-[#22a846] transition">
                            <option value="">-- Pilih Tong --</option>
                            <option value="semua" {{ request('tong_id')==='semua' ? 'selected':'' }}>Semua Tong</option>
                            @foreach($tongs as $tong)
                                <option value="{{ $tong->kode }}" {{ request('tong_id')===$tong->kode ? 'selected':'' }}>
                                    {{ $tong->kode }} – {{ $tong->nama }}
                                    @if($tong->lokasi) ({{ $tong->lokasi }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('tong_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Jenis laporan --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#1a3d28] mb-1.5">
                            Jenis Laporan <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis" required
                                class="w-full px-4 py-3 rounded-xl border {{ $errors->has('jenis') ? 'border-red-400 bg-red-50' : 'border-[#e0e8dc] bg-[#f8fbf8]' }} text-sm outline-none focus:border-[#22a846] transition">
                            <option value="">-- Pilih Jenis Laporan --</option>
                            <option value="semua"        {{ request('jenis')==='semua'        ? 'selected':'' }}>Semua (Sensor + Pengangkutan + Tong Penuh)</option>
                            <option value="sensor"       {{ request('jenis')==='sensor'       ? 'selected':'' }}>Data Sensor</option>
                            <option value="pengangkutan" {{ request('jenis')==='pengangkutan' ? 'selected':'' }}>Pengangkutan saja</option>
                            <option value="tong_penuh"   {{ request('jenis')==='tong_penuh'   ? 'selected':'' }}>Peringatan Tong Penuh</option>
                        </select>
                        @error('jenis')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="pt-2 flex gap-3">
                        <button type="submit"
                                class="flex items-center gap-2 bg-[#22a846] hover:bg-[#1a8c38] text-white font-bold px-6 py-3 rounded-xl transition shadow-sm">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            Unduh PDF
                        </button>
                        <a href="{{ route('riwayat') }}"
                           class="flex items-center gap-2 border border-[#e0e8dc] text-[#0d2e1a] font-semibold px-5 py-3 rounded-xl hover:bg-[#f8fbf8] transition text-sm">
                            Lihat Riwayat
                        </a>
                    </div>
                </form>
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

            {{-- Daftar tong tersedia --}}
            <div class="bg-white rounded-2xl shadow-sm border border-[#e5ede5] p-5">
                <h3 class="font-bold text-[#0d2e1a] text-sm mb-3">Tong Tersedia</h3>
                @forelse($tongs as $tong)
                <div class="flex items-center justify-between py-2 border-b border-[#f0f4f0] last:border-0">
                    <div>
                        <p class="text-xs font-semibold text-[#0d2e1a]">{{ $tong->kode }} – {{ $tong->nama }}</p>
                        <p class="text-xs text-[#7a9a85]">{{ $tong->lokasi ?? 'Lokasi tidak diset' }}</p>
                    </div>
                    <span class="text-xs font-bold px-2 py-1 rounded-full
                        {{ $tong->status === 'penuh' ? 'bg-red-100 text-red-700' : ($tong->status === 'hampir_penuh' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }}">
                        {{ $tong->persen }}%
                    </span>
                </div>
                @empty
                <p class="text-xs text-[#7a9a85]">Belum ada tong terdaftar.</p>
                @endforelse
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
                            Isi semua field bertanda <span class="text-red-500 font-bold">*</span> terlebih dahulu, lalu klik <strong>Unduh PDF</strong>.
                            PDF akan diunduh otomatis dalam orientasi landscape.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection