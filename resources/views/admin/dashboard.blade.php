@extends('layouts.app', ['title' => 'Dashboard Finansial & Operasional &bull; Admin POS Indomaret'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">
    
    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="p-2 bg-blue-100 text-blue-800 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </span>
                <h1 class="text-xl font-bold text-slate-800">Dashboard Finansial & Operasional Gerai</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Ikhtisar performa penjualan, margin laba kotor (*gross profit*), dan status inventaris toko secara real-time.
            </p>
        </div>

        <!-- Quick Sub-Navigation for Admin -->
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.reports') }}" class="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-lg shadow-xs transition-colors flex items-center space-x-1">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Laporan Penjualan</span>
            </a>

            <a href="{{ route('admin.products') }}" class="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-lg shadow-xs transition-colors flex items-center space-x-1">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span>Katalog Produk</span>
            </a>

            <a href="{{ route('admin.settings') }}" class="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-lg shadow-xs transition-colors flex items-center space-x-1">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Pengaturan Toko</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Omzet Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Omzet Hari Ini</span>
                <span class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-900">
                    Rp {{ number_format($todayRevenue, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    {{ $todayTransactionsCount }} transaksi selesai hari ini
                </div>
            </div>
            <div class="absolute bottom-0 inset-x-0 h-1 bg-blue-600"></div>
        </div>

        <!-- Laba Kotor (Gross Profit) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Laba Kotor (Gross Profit)</span>
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-emerald-600">
                    Rp {{ number_format($todayGrossProfit, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Margin: {{ $todayRevenue > 0 ? round(($todayGrossProfit / $todayRevenue) * 100, 1) : 0 }}% dari omzet
                </div>
            </div>
            <div class="absolute bottom-0 inset-x-0 h-1 bg-emerald-500"></div>
        </div>

        <!-- Omzet Minggu Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Omzet Minggu Ini</span>
                <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-900">
                    Rp {{ number_format($weekRevenue, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Akumulasi 7 hari operasional
                </div>
            </div>
            <div class="absolute bottom-0 inset-x-0 h-1 bg-indigo-500"></div>
        </div>

        <!-- Omzet Bulan Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Omzet Bulan Ini</span>
                <span class="p-2 bg-amber-50 text-amber-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-900">
                    Rp {{ number_format($monthRevenue, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Bulan {{ date('F Y') }}
                </div>
            </div>
            <div class="absolute bottom-0 inset-x-0 h-1 bg-amber-500"></div>
        </div>
    </div>

    <!-- Chart & Top Products Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 7-Day Revenue Trend (Visual CSS Bar Graph) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Tren Omzet Penjualan (7 Hari Terakhir)</h3>
                    <p class="text-xs text-slate-400">Statistik harian transaksi POS gerai</p>
                </div>
            </div>

            @php
                $maxRev = max(array_column($revenueTrend, 'revenue'));
                $maxRev = $maxRev > 0 ? $maxRev : 100000;
            @endphp
            <div class="h-56 mt-4 flex items-end justify-between gap-3 pt-6 px-2">
                @foreach($revenueTrend as $item)
                @php
                    $heightPercent = max(8, round(($item['revenue'] / $maxRev) * 100));
                @endphp
                <div class="flex-1 flex flex-col items-center group h-full justify-end">
                    <div class="text-[10px] font-bold text-slate-500 mb-1 group-hover:text-blue-700 transition-colors">
                        {{ $item['revenue'] > 0 ? 'Rp ' . number_format($item['revenue'] / 1000, 0) . 'k' : '0' }}
                    </div>
                    <div class="w-full bg-slate-100 rounded-t-lg h-full max-h-36 flex items-end overflow-hidden p-0.5">
                        <div class="w-full bg-gradient-to-t from-blue-700 to-blue-500 group-hover:from-red-600 group-hover:to-red-400 rounded-t-md transition-all duration-300"
                             style="height: {{ $heightPercent }}%;"></div>
                    </div>
                    <div class="text-center mt-2">
                        <div class="text-xs font-bold text-slate-700">{{ $item['day'] }}</div>
                        <div class="text-[10px] text-slate-400">{{ $item['date'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Top 5 Fast Selling Items -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
            <h3 class="text-sm font-bold text-slate-800 mb-1">Produk Terlaris (Top 5)</h3>
            <p class="text-xs text-slate-400 mb-4">Paling banyak dibeli pelanggan</p>

            <div class="space-y-3">
                @forelse($topSelling as $index => $item)
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-800 font-extrabold text-xs flex items-center justify-center">
                            {{ $index + 1 }}
                        </span>
                        <div>
                            <div class="text-xs font-bold text-slate-800 truncate max-w-[130px]">
                                {{ $item->product?->name }}
                            </div>
                            <div class="text-[10px] text-slate-400 font-medium">
                                {{ $item->total_sold }} {{ $item->product?->unit?->symbol ?? 'pcs' }} terjual
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs font-bold text-slate-900">
                            Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-8 text-center text-slate-400 text-xs">
                    Belum ada transaksi penjualan selesai.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Tables Row: Stok Kritis Alert & Audit Shift Kasir -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Low Stock Warning Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                    <h3 class="text-sm font-bold text-slate-800">Peringatan Stok Kritis (Menipis)</h3>
                </div>
                <a href="{{ route('warehouse.index', ['tab' => 'receipt']) }}" class="text-xs text-blue-700 font-bold hover:underline">
                    + Order PO Baru
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-2.5 px-4">Produk</th>
                            <th class="py-2.5 px-4">Kategori</th>
                            <th class="py-2.5 px-4">Sisa Stok</th>
                            <th class="py-2.5 px-4">Min. Stok</th>
                            <th class="py-2.5 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($criticalStock as $prod)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2.5 px-4 font-bold text-slate-800">{{ $prod->name }}</td>
                            <td class="py-2.5 px-4 text-slate-500">{{ $prod->category?->name }}</td>
                            <td class="py-2.5 px-4 font-black text-red-600">{{ $prod->stock }} {{ $prod->unit?->symbol }}</td>
                            <td class="py-2.5 px-4 text-slate-400">{{ $prod->min_stock }}</td>
                            <td class="py-2.5 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">
                                    Segera Restock
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">Seluruh stok barang dalam kondisi aman.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Cashier Shift Audits -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-sm font-bold text-slate-800">Audit Rekonsiliasi Shift Kasir</h3>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-2.5 px-4">Kasir</th>
                            <th class="py-2.5 px-4">Modal Awal</th>
                            <th class="py-2.5 px-4">Omzet Tunai</th>
                            <th class="py-2.5 px-4">Fisik Laci</th>
                            <th class="py-2.5 px-4">Selisih Kas</th>
                            <th class="py-2.5 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentShifts as $shift)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2.5 px-4 font-bold text-slate-800">{{ $shift->user?->name }}</td>
                            <td class="py-2.5 px-4 text-slate-600">Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-4 text-slate-600">Rp {{ number_format($shift->cash_sales_total, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-4 font-bold text-slate-800">
                                {{ $shift->actual_closing_cash !== null ? 'Rp ' . number_format($shift->actual_closing_cash, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-2.5 px-4 font-black {{ $shift->difference < 0 ? 'text-red-600' : ($shift->difference > 0 ? 'text-blue-600' : 'text-emerald-600') }}">
                                {{ $shift->difference > 0 ? '+' : '' }}{{ number_format($shift->difference, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $shift->status === 'open' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $shift->status === 'open' ? 'Masih Buka' : 'Selesai' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400">Belum ada riwayat shift kasir.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
