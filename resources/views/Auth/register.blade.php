<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoBin – Daftar Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .left-panel {
            background: linear-gradient(145deg, #0d2e1a 0%, #1a4d2e 40%, #0a1f12 100%);
            position: relative; overflow: hidden;
        }
        .left-panel::before {
            content: ''; position: absolute; top: -120px; right: -120px;
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(34,168,70,0.18) 0%, transparent 70%);
            border-radius: 50%;
        }
        .input-field {
            width: 100%; padding: 13px 16px; border-radius: 12px;
            border: 1.5px solid #e0e8dc; background: #fff;
            font-size: 14px; color: #1a2820; outline: none;
            transition: border-color .2s, box-shadow .2s;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .input-field::placeholder { color: #a8bfb0; }
        .input-field:focus { border-color: #22a846; box-shadow: 0 0 0 3px rgba(34,168,70,0.1); }
        .btn-primary {
            width: 100%; padding: 13px; border-radius: 12px;
            background: #22a846; color: white; font-weight: 700;
            font-size: 15px; border: none; cursor: pointer;
            transition: background .2s, box-shadow .2s;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .btn-primary:hover { background: #1a8c38; box-shadow: 0 4px 16px rgba(34,168,70,0.35); }
        .error-box { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 10px 14px; border-radius: 10px; font-size: 13px; }
    </style>
</head>
<body class="min-h-screen flex" style="background:#f5f8f3">

    {{-- LEFT: Branding --}}
    <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 left-panel flex-col justify-between p-12 relative z-10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-white/15 flex items-center justify-center">
                <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" class="w-5 h-5">
                    <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>
                </svg>
            </div>
            <span class="text-white font-bold text-lg">EcoBin</span>
        </div>
        <div>
            <h1 class="text-white font-extrabold text-4xl xl:text-5xl leading-tight mb-4">
                Pantau Tong<br>Sampah Pintar<br>dari Satu Dasbor.
            </h1>
            <p class="text-white/55 text-base leading-relaxed max-w-xs">
                Sistem monitoring berbasis IoT untuk pengelolaan sampah yang lebih efisien dan ramah lingkungan.
            </p>
        </div>
        <p class="text-white/25 text-xs">© 2026 EcoBin – Politeknik Negeri Batam</p>
    </div>

    {{-- RIGHT: Register Form --}}
    <div class="flex-1 flex items-center justify-center p-6">
        <div class="w-full max-w-md">
            <h2 class="text-3xl font-extrabold text-[#0d2e1a] mb-1">Daftar Admin</h2>
            <p class="text-[#7a9a85] text-sm mb-8">Buat akun admin untuk mengakses sistem.</p>

            @if ($errors->any())
                <div class="error-box mb-5">
                    @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-[#1a3d28] mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="Nama Anda" required class="input-field">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#1a3d28] mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="admin@ecobin.id" required class="input-field">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#1a3d28] mb-1.5">Password</label>
                    <input type="password" name="password"
                           placeholder="Min. 6 karakter" required class="input-field">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#1a3d28] mb-1.5">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation"
                           placeholder="Ulangi password" required class="input-field">
                </div>

                <button type="submit" class="btn-primary mt-2">Daftar</button>
            </form>

            <p class="text-center mt-6 text-sm text-[#7a9a85]">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-[#22a846] font-semibold hover:underline">Masuk</a>
            </p>
        </div>
    </div>
</body>
</html>
