@extends('layouts.app')
@section('title', 'Daftar Tong')

@section('content')
<div class="p-8">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-8">
        <div>
            <h1 class="text-[#0d2e1a] text-3xl font-extrabold">Daftar Tong Sampah</h1>
            <p class="text-[#7a9a85] text-sm mt-1">Kelola tong pintar yang terhubung ke sistem.</p>
        </div>
        <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                class="flex items-center gap-2 bg-[#22a846] hover:bg-[#1a8c38] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm"
                style="font-family:'Plus Jakarta Sans',sans-serif">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            + Tambah Tong
        </button>
    </div>

    {{-- Validasi error (dari tambah tong) --}}
    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
            @foreach($errors->all() as $e) <p>• {{ $e }}</p> @endforeach
        </div>
    @endif

    {{-- Grid Tong --}}
    @if($tongs->isEmpty())
        <div class="bg-white rounded-2xl border border-[#e5ede5] p-16 text-center shadow-sm">
            <div class="w-16 h-16 bg-[#f2f6f0] rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-[#a8bfb0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/>
                </svg>
            </div>
            <p class="text-[#0d2e1a] font-bold text-lg mb-1">Belum ada tong terdaftar</p>
            <p class="text-[#7a9a85] text-sm mb-4">Tambahkan tong sampah pintar pertama kamu.</p>
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                    class="bg-[#22a846] hover:bg-[#1a8c38] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition"
                    style="font-family:'Plus Jakarta Sans',sans-serif">
                + Tambah Tong Sekarang
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($tongs as $tong)
            <div class="bg-white rounded-2xl shadow-sm border border-[#e5ede5] p-5 hover:shadow-md transition-shadow">

                {{-- Header card --}}
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-[#a8bfb0] text-xs font-medium">{{ $tong->kode }}</p>
                        <h3 class="font-bold text-[#0d2e1a] text-sm leading-tight mt-0.5">{{ $tong->nama }}</h3>
                    </div>
                    {{-- Status badge --}}
                    @if($tong->status === 'penuh')
                        <span class="text-xs font-semibold text-red-600 bg-red-50 px-2.5 py-1 rounded-full">● Penuh</span>
                    @elseif($tong->status === 'hampir_penuh')
                        <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">● Hampir Penuh</span>
                    @else
                        <span class="text-xs font-semibold text-green-600 bg-green-50 px-2.5 py-1 rounded-full">● Normal</span>
                    @endif
                </div>

                {{-- Lokasi --}}
                @if($tong->lokasi)
                <div class="flex items-center gap-1.5 text-xs text-[#7a9a85] mb-4">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
                    </svg>
                    {{ $tong->lokasi }}
                </div>
                @endif

                {{-- Progress bar kapasitas --}}
                <div class="mb-1 flex justify-between items-center">
                    <span class="text-xs text-[#7a9a85]">Kapasitas {{ $tong->kapasitas }}L</span>
                    <span class="text-sm font-bold text-[#0d2e1a]">{{ $tong->persen }}%</span>
                </div>
                <div class="w-full bg-[#f2f6f0] rounded-full h-2 mb-5">
                    <div class="h-2 rounded-full transition-all
                        {{ $tong->persen >= 90 ? 'bg-red-500' : ($tong->persen >= 70 ? 'bg-amber-400' : 'bg-[#22a846]') }}"
                         style="width:{{ $tong->persen }}%"></div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 pt-4 border-t border-[#f5f8f3]">

                    {{-- Catat Pengangkutan --}}
                    <form method="POST"
                          action="{{ route('catat-pengangkutan', $tong->kode) }}"
                          class="flex-1"
                          onsubmit="return confirm('Catat pengangkutan tong {{ $tong->kode }}?\nKapasitas akan direset ke 0%.')">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-1.5 text-xs font-semibold text-[#22a846] border border-[#22a846]/30 hover:bg-[#f0faf4] px-3 py-2 rounded-xl transition"
                                style="font-family:'Plus Jakarta Sans',sans-serif">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 7h14l1 8H4L5 7z"/><path d="M8 7V5a4 4 0 018 0v2"/>
                            </svg>
                            Catat Pengangkutan
                        </button>
                    </form>

                    {{-- Hapus Tong --}}
                    <form method="POST"
                          action="{{ route('hapus-tong', $tong->kode) }}"
                          onsubmit="return confirm('Hapus tong {{ $tong->kode }} secara permanen?\nSemua riwayat tong ini akan ikut terhapus.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="p-2 text-[#c8d8c8] hover:text-red-400 hover:bg-red-50 rounded-xl transition"
                                title="Hapus tong">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14H6L5 6"/>
                                <path d="M10 11v6M14 11v6M9 6V4h6v2"/>
                            </svg>
                        </button>
                    </form>

                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>

{{-- ===== MODAL TAMBAH TONG ===== --}}
<div id="modalTambah"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background:rgba(13,46,26,0.5);backdrop-filter:blur(4px)">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 relative">

        <button onclick="document.getElementById('modalTambah').classList.add('hidden')"
                class="absolute top-4 right-4 text-[#a8bfb0] hover:text-[#0d2e1a] transition">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>

        <h2 class="text-xl font-extrabold text-[#0d2e1a] mb-1">Tambah Tong Pintar</h2>
        <p class="text-[#7a9a85] text-sm mb-5">Isi detail tong yang akan dihubungkan ke sistem.</p>

        <form method="POST" action="{{ route('tambah-tong') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-[#1a3d28] mb-1.5">
                    Kode <span class="text-[#a8bfb0] font-normal">(contoh: TSP-07)</span>
                </label>
                <input type="text" name="kode" placeholder="TSP-07" required
                       value="{{ old('kode') }}"
                       class="w-full px-4 py-3 rounded-xl border border-[#e0e8dc] bg-[#f8fbf8] text-sm outline-none focus:border-[#22a846] focus:ring-2 focus:ring-[#22a846]/10 transition"
                       style="font-family:'Plus Jakarta Sans',sans-serif">
            </div>

            <div>
                <label class="block text-sm font-semibold text-[#1a3d28] mb-1.5">Nama Tong</label>
                <input type="text" name="nama" placeholder="Tong Pintar ..."
                       required value="{{ old('nama') }}"
                       class="w-full px-4 py-3 rounded-xl border border-[#e0e8dc] bg-[#f8fbf8] text-sm outline-none focus:border-[#22a846] focus:ring-2 focus:ring-[#22a846]/10 transition"
                       style="font-family:'Plus Jakarta Sans',sans-serif">
            </div>

            <div>
                <label class="block text-sm font-semibold text-[#1a3d28] mb-1.5">Lokasi</label>
                <input type="text" name="lokasi" placeholder="Gedung A – Lobi"
                       value="{{ old('lokasi') }}"
                       class="w-full px-4 py-3 rounded-xl border border-[#e0e8dc] bg-[#f8fbf8] text-sm outline-none focus:border-[#22a846] focus:ring-2 focus:ring-[#22a846]/10 transition"
                       style="font-family:'Plus Jakarta Sans',sans-serif">
            </div>

            <div>
                <label class="block text-sm font-semibold text-[#1a3d28] mb-1.5">Kapasitas (Liter)</label>
                <input type="number" name="kapasitas" value="{{ old('kapasitas', 60) }}" min="1" required
                       class="w-full px-4 py-3 rounded-xl border border-[#e0e8dc] bg-[#f8fbf8] text-sm outline-none focus:border-[#22a846] transition"
                       style="font-family:'Plus Jakarta Sans',sans-serif">
            </div>

            <button type="submit"
                    class="w-full py-3 bg-[#22a846] hover:bg-[#1a8c38] text-white font-bold rounded-xl transition shadow-sm"
                    style="font-family:'Plus Jakarta Sans',sans-serif">
                Simpan Tong
            </button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Buka modal otomatis jika ada error validasi (setelah redirect balik)
@if($errors->any())
    document.getElementById('modalTambah').classList.remove('hidden');
@endif

// Tutup modal saat klik backdrop
document.getElementById('modalTambah').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
</script>
@endpush
