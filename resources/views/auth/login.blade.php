<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login &bull; Sistem POS Indomaret</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="h-full flex items-center justify-center p-4 bg-gradient-to-br from-slate-100 via-blue-50/40 to-slate-100">

    <div class="w-full max-w-md">
        <!-- Brand Header Card -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center p-3 bg-white rounded-2xl shadow-sm border border-slate-200/80 mb-3">
                <span class="text-3xl font-black text-blue-700 tracking-tighter">Indo</span>
                <span class="text-3xl font-black text-red-600 tracking-tighter">maret</span>
            </div>
            <h1 class="text-xl font-bold text-slate-800">Point of Sale System</h1>
            <p class="text-xs text-slate-500 mt-1">Multi-Role Retail Operations & Inventory Management</p>
        </div>

        <!-- Main Login Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200/80 p-6 sm:p-8">
            <h2 class="text-base font-bold text-slate-800 mb-1">Masuk ke Sistem</h2>
            <p class="text-xs text-slate-500 mb-6">Gunakan ID Kasir / NIM / NIK atau Email Karyawan</p>

            @if(session('success'))
                <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded-xl font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-xl font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="login" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        ID Kasir / Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input type="text" 
                               name="login" 
                               id="login" 
                               required 
                               autofocus
                               placeholder="Contoh: KSR001 atau admin@indomaret.co.id" 
                               value="{{ old('login') }}"
                               class="w-full pl-10 pr-3.5 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               required 
                               placeholder="••••••••"
                               class="w-full pl-10 pr-3.5 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                        <span class="ml-2 text-slate-600">Ingat sesi saya</span>
                    </label>
                </div>

                <button type="submit" 
                        class="w-full py-2.5 px-4 bg-blue-700 hover:bg-blue-800 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center space-x-2">
                    <span>Masuk ke POS</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>
            </form>

            <!-- Quick Demo Login Switcher -->
            <div class="mt-8 pt-6 border-t border-slate-100">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 text-center mb-3">
                    Akses Cepat Pengujian (1-Click Demo)
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <a href="{{ route('quick-login', 'cashier') }}" 
                       class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-blue-50 hover:border-blue-300 transition-all text-center group">
                        <span class="text-xs font-bold text-slate-800 group-hover:text-blue-700">Kasir</span>
                        <span class="text-[10px] text-slate-400 font-mono mt-0.5">KSR001</span>
                    </a>

                    <a href="{{ route('quick-login', 'warehouse') }}" 
                       class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-amber-50 hover:border-amber-300 transition-all text-center group">
                        <span class="text-xs font-bold text-slate-800 group-hover:text-amber-700">Gudang</span>
                        <span class="text-[10px] text-slate-400 font-mono mt-0.5">GDG001</span>
                    </a>

                    <a href="{{ route('quick-login', 'admin') }}" 
                       class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-red-50 hover:border-red-300 transition-all text-center group">
                        <span class="text-xs font-bold text-slate-800 group-hover:text-red-700">Admin</span>
                        <span class="text-[10px] text-slate-400 font-mono mt-0.5">ADM001</span>
                    </a>
                </div>
                <p class="text-[11px] text-slate-400 text-center mt-3">Default password: <code class="font-mono text-slate-600 bg-slate-100 px-1 py-0.5 rounded">password</code></p>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-xs text-slate-400">
            &copy; {{ date('Y') }} POS Indomaret &bull; Retail Architecture
        </div>
    </div>

</body>
</html>
