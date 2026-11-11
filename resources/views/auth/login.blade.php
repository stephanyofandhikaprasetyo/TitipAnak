<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - TitipAnak</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --ta-ink: #1B1B1B;
            --ta-muted: #6B6B6B;
            --ta-paper: #FFFFFF;
            --ta-cream: #FFF6D6;
            --ta-yellow: #FFD43B;
            --ta-coral: #FF9E80;
            --ta-border: 1.5px solid var(--ta-ink);
            --ta-radius: 10px;
            --ta-font: "Poppins", sans-serif;
        }
        body {
            font-family: var(--ta-font);
            background-color: var(--ta-paper);
            color: var(--ta-ink);
        }
    </style>
</head>
<body class="bg-[#FFF6D6] min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-6 sm:p-8 space-y-6 shadow-none">
        
        {{-- Header Logo & Title --}}
        <div class="text-center space-y-3">
            <div class="inline-grid place-items-center w-14 h-14 rounded-[10px] bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] font-bold text-xl text-[#1B1B1B] mx-auto">
                TA
            </div>
            <div>
                <h1 class="text-2xl font-bold text-[#1B1B1B]">Masuk ke TitipAnak</h1>
                <p class="text-sm text-[#6B6B6B] mt-1">Aplikasi Manajemen Daycare</p>
            </div>
        </div>

        {{-- Banner RBAC TitipAnak --}}
        <div class="flex items-center gap-3 bg-[#1B1B1B] text-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-3 text-xs font-semibold">
            <div class="w-8 h-8 rounded-full bg-white border border-[#1B1B1B] grid place-items-center text-sm flex-none text-[#1B1B1B]">
                🔑
            </div>
            <span>Satu login untuk semua. Sistem membaca peran dan mengarahkan ke dashboard.</span>
        </div>

        {{-- Alert Errors --}}
        @if ($errors->any())
            <div class="flex gap-3 p-3.5 rounded-[10px] bg-[#FF9E80] border-[1.5px] border-[#1B1B1B] text-xs font-semibold text-[#1B1B1B]">
                <span class="text-base flex-none">⚠️</span>
                <div class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Form Login --}}
        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf

            {{-- Input Email --}}
            <div class="grid gap-1.5">
                <label for="email" class="font-semibold text-xs text-[#1B1B1B]">Alamat Email</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}"
                       required 
                       autofocus
                       placeholder="orangtua@titipanak.com"
                       class="w-full text-sm bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-3 text-[#1B1B1B] placeholder-[#6B6B6B] focus:outline-none focus:ring-2 focus:ring-[#FFD43B] focus:bg-[#FFF6D6]">
            </div>

            {{-- Input Password --}}
            <div class="grid gap-1.5">
                <label for="password" class="font-semibold text-xs text-[#1B1B1B]">Password</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required 
                       placeholder="••••••••"
                       class="w-full text-sm bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-3 text-[#1B1B1B] placeholder-[#6B6B6B] focus:outline-none focus:ring-2 focus:ring-[#FFD43B] focus:bg-[#FFF6D6]">
            </div>

            {{-- Tombol Login --}}
            <button type="submit" 
                    class="w-full font-semibold text-sm py-3 px-5 rounded-[10px] bg-[#FFD43B] text-[#1B1B1B] border-[1.5px] border-[#1B1B1B] hover:-translate-y-0.5 active:translate-y-0 transition-transform cursor-pointer">
                Masuk Sekarang
            </button>
        </form>

        {{-- Footer Guidelines --}}
        <div class="text-center pt-3 border-t-[1.5px] border-[#1B1B1B]/10">
            <span class="text-xs text-[#6B6B6B]">TitipAnak Design System · Kelompok A SI48EXT</span>
        </div>
    </div>

</body>
</html>