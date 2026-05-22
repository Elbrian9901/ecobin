<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EcoBin – @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f2f6f0; }
        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 16px; border-radius: 10px;
            color: rgba(255,255,255,0.65); font-size: 14px; font-weight: 500;
            transition: all .2s; cursor: pointer; text-decoration: none;
        }
        .sidebar-link:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .sidebar-link.active { background: #22a846; color: #fff; }
        .sidebar-link svg { width: 17px; height: 17px; flex-shrink: 0; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #b3ddbf; border-radius: 99px; }
    </style>
    @stack('head')
</head>
<body class="flex h-screen overflow-hidden">

{{-- ===== SIDEBAR ===== --}}
<aside class="w-52 flex-shrink-0 flex flex-col h-full"
       style="background: linear-gradient(180deg,#12331e 0%,#0a1f12 100%);">

    {{-- Logo --}}
    <div class="flex items-center gap-2.5 px-5 py-5 border-b border-white/10">
        <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" class="w-4 h-4">
                <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>
            </svg>
        </div>
        <div>
            <p class="text-white font-bold text-sm leading-tight">EcoBin</p>
            <p class="text-white/40 text-[10px] leading-tight">Tong Sampah Pintar</p>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

        <a href="{{ route('dashboard') }}"
           class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
            </svg>
            Dashboard
        </a>

        <a href="{{ route('riwayat') }}"
           class="sidebar-link {{ request()->routeIs('riwayat') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
            Riwayat
        </a>

        <a href="{{ route('daftar-tong') }}"
           class="sidebar-link {{ request()->routeIs('daftar-tong') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/>
            </svg>
            Daftar Tong
        </a>

        <a href="{{ route('notifikasi') }}"
           class="sidebar-link {{ request()->routeIs('notifikasi') ? 'active' : '' }} relative">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 01-3.46 0"/>
            </svg>
            Notifikasi
            @php $unread = \App\Models\Notifikasi::where('sudah_dibaca',false)->count(); @endphp
            @if($unread > 0)
                <span class="ml-auto bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                    {{ $unread }}
                </span>
            @endif
        </a>

        <a href="{{ route('laporan') }}"
           class="sidebar-link {{ request()->routeIs('laporan') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
            Laporan PDF
        </a>

    </nav>

    {{-- User & Logout --}}
    <div class="border-t border-white/10 px-3 py-4 space-y-1">
        <div class="px-3 py-2">
            <p class="text-white/80 text-xs font-medium truncate">{{ Auth::user()->email ?? 'admin@ecobin.id' }}</p>
            <p class="text-white/35 text-[10px]">Administrator</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-link w-full text-left">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Keluar
            </button>
        </form>
    </div>
</aside>

{{-- ===== MAIN CONTENT ===== --}}
<main class="flex-1 overflow-y-auto">

    {{-- Flash messages --}}
    @if(session('success'))
        <div id="flash-success"
             class="fixed top-5 right-5 z-50 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl shadow-lg text-sm font-medium max-w-sm"
             style="font-family:'Plus Jakarta Sans',sans-serif">
            <svg class="w-4 h-4 text-green-500 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div id="flash-error"
             class="fixed top-5 right-5 z-50 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-lg text-sm font-medium max-w-sm"
             style="font-family:'Plus Jakarta Sans',sans-serif">
            <svg class="w-4 h-4 text-red-500 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @yield('content')
</main>

<script>
// Auto hide flash after 3s
setTimeout(() => {
    ['flash-success','flash-error'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.style.transition='opacity .5s'; el.style.opacity='0'; setTimeout(()=>el.remove(),500); }
    });
}, 3000);
</script>

@stack('scripts')
</body>
</html>
