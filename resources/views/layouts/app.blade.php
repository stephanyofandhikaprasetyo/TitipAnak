<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TitipAnak') — TitipAnak</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-white text-ink antialiased">

    @php
        $pageTitle = trim($__env->yieldContent('page-title'))
                     ?: trim($__env->yieldContent('title'))
                     ?: 'Dashboard';
    @endphp

    <div class="min-h-screen flex">

        {{-- Backdrop (mobile) --}}
        <div id="sidebar-backdrop"
             class="hidden fixed inset-0 bg-ink/50 z-30 md:hidden"
             onclick="toggleSidebar()"></div>

        {{-- ================= SIDEBAR ================= --}}
        <aside id="sidebar"
               class="fixed md:sticky top-0 left-0 z-40 h-screen w-64 bg-cream border-r-[1.5px] border-ink
                      -translate-x-full md:translate-x-0 transition-transform duration-200
                      flex flex-col">

            {{-- Brand --}}
            <div class="p-5 border-b-[1.5px] border-ink flex-none">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 font-bold text-lg text-ink">
                    <span class="inline-flex items-center justify-center w-10 h-10
                                 bg-yellow border-[1.5px] border-ink rounded-ta
                                 font-bold text-sm">TA</span>
                    TitipAnak
                </a>
            </div>

            {{-- Menu --}}
            <nav class="flex-1 overflow-y-auto p-4 space-y-1">
                @php
                    $menu = [
                        ['label' => 'Dashboard',    'route' => 'orangtua.dashboard',         'icon' => '📊'],
                        ['label' => 'Data Anak',    'route' => 'orangtua.anak.index',        'icon' => '👶'],
                        ['label' => 'Penjemput',    'route' => 'orangtua.penjemputan.create', 'icon' => '🚗', 'active' => 'orangtua.penjemputan.*'],
                        ['label' => 'Tagihan',      'route' => 'orangtua.tagihan.index',     'icon' => '💳'],
                        ['label' => 'Laporan Anak', 'route' => 'orangtua.laporan.index',     'icon' => '📋'],
                    ];
                @endphp

                @foreach ($menu as $item)
                    @php
                        $pattern  = $item['active'] ?? $item['route'];
                        $isActive = request()->routeIs($pattern);
                        $exists   = \Route::has($item['route']);
                        $href     = $exists ? route($item['route']) : '#';
                    @endphp
                    <a href="{{ $href }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-ta text-sm font-medium
                              border-[1.5px] transition
                              {{ $isActive
                                    ? 'bg-yellow border-ink font-bold text-ink'
                                    : 'border-transparent text-ink hover:bg-white hover:border-ink' }}
                              {{ !$exists ? 'opacity-40 pointer-events-none' : '' }}">
                        <span aria-hidden="true">{{ $item['icon'] }}</span>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            {{-- User info + Logout --}}
            <div class="p-4 border-t-[1.5px] border-ink flex-none">
                <div class="flex items-center gap-3 mb-3">
                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-full
                                bg-mint border-[1.5px] border-ink font-bold text-ink">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-sm font-semibold text-ink truncate">
                            {{ auth()->user()->name ?? 'User' }}
                        </div>
                        <div class="text-xs text-muted truncate">
                            {{ auth()->user()->email ?? '' }}
                        </div>
                    </div>
                </div>

                @if (\Route::has('logout'))
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 text-sm font-semibold
                                       px-3 py-2 bg-white border-[1.5px] border-ink rounded-ta
                                       text-ink hover:bg-coral transition">
                            <span aria-hidden="true">🚪</span> Keluar
                        </button>
                    </form>
                @endif
            </div>
        </aside>

        {{-- ================= MAIN ================= --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Topbar --}}
            <header class="sticky top-0 z-20 bg-white border-b-[1.5px] border-ink">
                <div class="flex items-center justify-between gap-3 px-4 md:px-6 py-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <button type="button"
                                onclick="toggleSidebar()"
                                class="md:hidden inline-flex items-center justify-center
                                       w-10 h-10 bg-white border-[1.5px] border-ink rounded-ta text-ink"
                                aria-label="Buka menu">☰</button>
                        <h1 class="text-base md:text-lg font-bold text-ink truncate">
                            {{ $pageTitle }}
                        </h1>
                    </div>
                    <span class="inline-flex items-center text-xs font-semibold
                                 px-3 py-1.5 border-[1.5px] border-ink rounded-full
                                 bg-mint text-ink whitespace-nowrap">
                        Orang Tua
                    </span>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1">
                @yield('content')
            </main>

            {{-- Footer --}}
            <footer class="border-t-[1.5px] border-ink bg-cream px-4 md:px-6 py-3 text-xs text-muted">
                &copy; {{ date('Y') }} TitipAnak &middot; Kelompok A SI48EXT &middot; Tugas Besar PPL
            </footer>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('sidebar-backdrop').classList.toggle('hidden');
        }
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 768) {
                document.getElementById('sidebar').classList.remove('-translate-x-full');
                document.getElementById('sidebar-backdrop').classList.add('hidden');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>


