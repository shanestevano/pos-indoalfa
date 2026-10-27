@extends('layouts.app', ['title' => 'Laporan Penjualan & Margin Keuntungan &bull; POS Indomaret'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">
    
    <!-- Title and Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Laporan Penjualan & Margin Keuntungan (Gross Profit)</h1>
            <p class="text-xs text-slate-500 mt-1">
                Analisis transaksi penjualan kasir, rincian omzet, HPP, serta laba kotor per transaksi.
            </p>
        </div>

        <div class="flex items-center space-x-2">
            <button type="button" 
                    onclick="window.print()" 
                    class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Cetak / Ekspor PDF</span>
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('admin.reports') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end">
            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Petugas Kasir</label>
                <select name="cashier_id" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                    <option value="">-- Semua Kasir --</option>
                    @foreach($cashiers as $c)
                    <option value="{{ $c->id }}" {{ $cashierId == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->employee_code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Metode Bayar</label>
                <select name="payment_method" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                    <option value="">-- Semua Metode --</option>
                    <option value="cash" {{ $paymentMethod === 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                    <option value="qris" {{ $paymentMethod === 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="edc" {{ $paymentMethod === 'edc' ? 'selected' : '' }}>EDC / Debit</option>
                </select>
            </div>

            <div>
                <button type="submit" class="w-full py-2 px-4 bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs rounded-xl transition-colors shadow-xs">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Aggregate Metric Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Omzet Penjualan</span>
            <div class="text-xl font-black text-slate-900 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Modal (HPP)</span>
            <div class="text-xl font-black text-slate-600 mt-1">Rp {{ number_format($totalCost, 0, ',', '.') }}</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Laba Kotor (Gross Profit)</span>
            <div class="text-xl font-black text-emerald-600 mt-1">Rp {{ number_format($totalGrossProfit, 0, ',', '.') }}</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Diskon Diberikan</span>
            <div class="text-xl font-black text-red-600 mt-1">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800">Daftar Struk Transaksi Selesai</h3>
            <span class="text-xs text-slate-500 font-medium">{{ $sales->total() }} transaksi ditemukan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">No. Struk</th>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Kasir</th>
                        <th class="py-3 px-4">Metode Bayar</th>
                        <th class="py-3 px-4">Item Terjual</th>
                        <th class="py-3 px-4 text-right">Subtotal</th>
                        <th class="py-3 px-4 text-right">Total Bayar</th>
                        <th class="py-3 px-4 text-right">HPP</th>
                        <th class="py-3 px-4 text-right">Laba Kotor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-4 font-mono font-bold text-blue-700">{{ $sale->invoice_number }}</td>
                        <td class="py-3 px-4 text-slate-500">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 px-4 font-medium text-slate-800">{{ $sale->user?->name }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $sale->payment_method === 'cash' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $sale->payment_method }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-600">
                            {{ $sale->items->sum('quantity') }} pcs ({{ $sale->items->count() }} jenis)
                        </td>
                        <td class="py-3 px-4 text-right text-slate-600">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-right font-extrabold text-slate-900">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-right text-slate-500">Rp {{ number_format($sale->total_cost, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-right font-extrabold text-emerald-600">
                            Rp {{ number_format($sale->gross_profit, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-8 text-center text-slate-400">Tidak ada transaksi penjualan pada rentang tanggal ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-t border-slate-100">
            {{ $sales->withQueryString()->links() }}
        </div>
    </div>

</div>
@endsection
