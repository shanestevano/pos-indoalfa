<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Sistem POS Indomaret' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .font-mono-receipt {
            font-family: 'JetBrains Mono', monospace;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            #thermal-receipt, #thermal-receipt * {
                visibility: visible;
            }
            #thermal-receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 58mm;
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body class="h-full text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs">
        <div class="px-4 sm:px-6 flex items-center justify-between h-14">
            <!-- Brand & Logo -->
            <div class="flex items-center space-x-3">
                <a href="{{ url('/') }}" class="flex items-center space-x-2">
                    <div class="flex items-center tracking-tighter font-extrabold text-xl">
                        <span class="text-blue-700">Indo</span><span class="text-red-600">maret</span>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 bg-yellow-400 text-slate-900 rounded-sm">
                        POS Retail
                    </span>
                </a>

                @auth
                <!-- Breadcrumbs / Active Context -->
                <div class="hidden md:flex items-center text-xs text-slate-400 pl-4 border-l border-slate-200 space-x-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="font-medium text-slate-600">{{ \App\Models\StoreSetting::current()->store_name }}</span>
                    <span>&bull;</span>
                    <span class="text-slate-500">{{ \App\Models\StoreSetting::current()->store_code }}</span>
                </div>
                @endauth
            </div>

            @auth
            <!-- Navigation Links according to Role -->
            <nav class="flex items-center space-x-1 sm:space-x-3">
                @if(auth()->user()->isAdmin() || auth()->user()->isCashier())
                <a href="{{ route('pos.index') }}" 
                   class="px-3 py-1.5 text-xs font-bold rounded-lg transition-colors flex items-center space-x-1.5 {{ request()->routeIs('pos.*') ? 'bg-blue-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span>Terminal Kasir</span>
                </a>
                @endif

                @if(auth()->user()->isAdmin() || auth()->user()->isWarehouse())
                <a href="{{ route('warehouse.index') }}" 
                   class="px-3 py-1.5 text-xs font-bold rounded-lg transition-colors flex items-center space-x-1.5 {{ request()->routeIs('warehouse.*') ? 'bg-blue-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span>Gudang & Stok</span>
                </a>
                @endif

                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" 
                   class="px-3 py-1.5 text-xs font-bold rounded-lg transition-colors flex items-center space-x-1.5 {{ request()->routeIs('admin.*') ? 'bg-blue-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>Admin Manager</span>
                </a>
                @endif

                <!-- User profile badge & logout -->
                <div class="flex items-center pl-3 border-l border-slate-200 space-x-2">
                    <div class="text-right hidden sm:block">
                        <div class="text-xs font-bold text-slate-800">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold">
                            {{ auth()->user()->employee_code }} &bull; {{ auth()->user()->role }}
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" 
                                title="Keluar / Logout"
                                class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </nav>
            @endauth
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="min-h-[calc(100vh-3.5rem)]">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Global POS Toast Alert System -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 flex flex-col space-y-2 pointer-events-none"></div>

    @livewireScripts

    <!-- Sound & Keyboard Shortcuts Script -->
    <script>
        // Web Audio synthetic beeps
        function playBeep(freq = 880, type = 'sine', duration = 0.1) {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = type;
                osc.frequency.value = freq;
                gain.gain.setValueAtTime(0.15, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + duration);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + duration);
            } catch(e) {}
        }

        window.addEventListener('play-chime', () => {
            playBeep(523.25, 'triangle', 0.12); // C5
            setTimeout(() => playBeep(659.25, 'triangle', 0.15), 100); // E5
            setTimeout(() => playBeep(783.99, 'triangle', 0.25), 200); // G5
        });

        window.addEventListener('pos-alert', event => {
            const detail = Array.isArray(event.detail) ? event.detail[0] : event.detail;
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            const type = detail.type || 'info';
            const bgClass = type === 'error' ? 'bg-red-600 text-white' : 
                            type === 'warning' ? 'bg-amber-500 text-white' : 
                            type === 'success' ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-white';

            toast.className = `px-4 py-3 rounded-lg shadow-lg text-xs font-semibold flex items-center space-x-2 transition-all duration-300 transform translate-y-2 opacity-0 pointer-events-auto ${bgClass}`;
            toast.innerHTML = `<span>${detail.message}</span>`;
            
            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }, 10);

            if (type === 'error') playBeep(220, 'sawtooth', 0.3);
            else if (type === 'warning') playBeep(440, 'sine', 0.2);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        });
    </script>
    @stack('scripts')
</body>
</html>
