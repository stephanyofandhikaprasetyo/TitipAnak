<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? View::yieldContent('title', 'Penjemputan') }} — TitipAnak Orang Tua</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --ta-ink: #1B1B1B;
            --ta-muted: #6B6B6B;
            --ta-paper: #FFFFFF;
            --ta-cream: #FFF6D6;
            --ta-yellow: #FFD43B;
            --ta-blue: #A8D4FF;
            --ta-mint: #A8E6CF;
            --ta-lavender: #CDBDFF;
            --ta-coral: #FF9E80;
            --ta-link: #1B6FD1;
        }

        body {
            font-family: "Poppins", ui-sans-serif, system-ui, -apple-system, sans-serif;
            background-color: var(--ta-paper);
            color: var(--ta-ink);
            -webkit-font-smoothing: antialiased;
        }
    </style>
</head>
<body class="min-h-screen bg-white text-[#1B1B1B]">
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-[248px_minmax(0,1fr)]">
        {{-- Sidebar Krem --}}
        <aside class="bg-[#FFF6D6] border-b-[1.5px] lg:border-b-0 lg:border-r-[1.5px] border-[#1B1B1B] p-4 lg:p-6 flex flex-col justify-between gap-4">
            <div>
                {{-- Brand --}}
<div class="flex items-center justify-between lg:justify-start gap-3 mb-4 lg:mb-6">
    <a href="{{ url('/orang-tua/penjemput') }}" wire:navigate class="flex items-center gap-3 no-underline text-[#1B1B1B]">
        <span class="inline-grid place-items-center w-10 h-10 rounded-[10px] bg-[#FFD43B] border-[1.5px] border-[#1B1B1B] font-bold text-sm">
            TA
        </span>
        <div>
            <div class="font-bold text-lg leading-tight">TitipAnak</div>
            <div class="text-xs text-[#6B6B6B]">Portal Orang Tua</div>
        </div>
    </a>

    <span class="lg:hidden inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full bg-[#A8E6CF] border border-[#1B1B1B]">
        Orang Tua
    </span>
</div>

{{-- Menu Navigasi --}}
<nav aria-label="Menu Orang Tua" class="flex lg:flex-col gap-2 overflow-x-auto pb-1 lg:pb-0">
    <a href="{{ url('/orang-tua/penjemput') }}"
       wire:navigate
       class="whitespace-nowrap flex items-center gap-2.5 px-3.5 py-2.5 rounded-[10px] text-sm transition-colors border-[1.5px] {{ request()->is('orang-tua/penjemput') && !request()->boolean('buat') ? 'bg-[#FFD43B] border-[#1B1B1B] font-bold' : 'border-transparent hover:bg-white hover:border-[#1B1B1B] font-medium' }}">
        <span>🚗</span>
        <span>Riwayat Penjemputan</span>
    </a>

    <a href="{{ url('/orang-tua/penjemput?buat=1') }}"
       wire:navigate
       class="whitespace-nowrap flex items-center gap-2.5 px-3.5 py-2.5 rounded-[10px] text-sm transition-colors border-[1.5px] {{ request()->boolean('buat') ? 'bg-[#FFD43B] border-[#1B1B1B] font-bold' : 'border-transparent hover:bg-white hover:border-[#1B1B1B] font-medium' }}">
        <span>➕</span>
        <span>Ajukan Penjemputan</span>
    </a>
</nav>
            </div>

            {{-- Info Akun & Logout --}}
            <div class="hidden lg:grid gap-3 pt-4 border-t border-[#1B1B1B]">
                <div class="bg-white border-[1.5px] border-[#1B1B1B] rounded-[10px] p-3">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-[#A8E6CF] border border-[#1B1B1B]">
                            Orang Tua
                        </span>
                    </div>
                    <div class="font-bold text-sm truncate">{{ Auth::user()?->nama }}</div>
                    <div class="text-xs text-[#6B6B6B] truncate">{{ Auth::user()?->email }}</div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 font-semibold text-sm px-4 py-2.5 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] hover:bg-[#FF9E80] transition-colors cursor-pointer">
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Konten Utama --}}
        <main class="p-4 sm:p-6 lg:p-8">
            <div class="max-w-[1120px] mx-auto space-y-6">
                {{-- Mobile Logout Bar --}}
                <div class="flex lg:hidden items-center justify-between gap-3 bg-[#FFF6D6] border-[1.5px] border-[#1B1B1B] rounded-[10px] px-4 py-2.5">
                    <div class="min-w-0">
                        <div class="font-bold text-sm truncate">{{ Auth::user()?->nama }}</div>
                        <div class="text-xs text-[#6B6B6B] truncate">{{ Auth::user()?->email }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="font-semibold text-xs px-3 py-2 rounded-[10px] bg-white border-[1.5px] border-[#1B1B1B] hover:bg-[#FF9E80] cursor-pointer">
                            Keluar
                        </button>
                    </form>
                </div>

                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
