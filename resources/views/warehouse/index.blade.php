@extends('layouts.app', ['title' => 'Manajemen Gudang & Stok &bull; POS Indomaret'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-6" x-data="{ tab: '{{ $tab }}' }">
    
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="p-2 bg-amber-100 text-amber-800 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </span>
                <h1 class="text-xl font-bold text-slate-800">Modul Gudang & Manajemen Inventaris</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">Penerimaan barang dari supplier, stock opname koreksi stok, dan buku besar audit trail.</p>
        </div>

        <!-- Tab Pills -->
        <div class="flex items-center space-x-1.5 bg-white p-1 rounded-xl border border-slate-200 shadow-xs">
            <button type="button" 
                    @click="tab = 'receipt'"
                    :class="tab === 'receipt' ? 'bg-blue-700 text-white font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                    class="px-3.5 py-1.5 text-xs rounded-lg transition-colors flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20"></path>
                </svg>
                <span>Penerimaan Barang</span>
            </button>

            <button type="button" 
                    @click="tab = 'adjustment'"
                    :class="tab === 'adjustment' ? 'bg-blue-700 text-white font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                    class="px-3.5 py-1.5 text-xs rounded-lg transition-colors flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>Stock Opname / Koreksi</span>
            </button>

            <button type="button" 
                    @click="tab = 'mutations'"
                    :class="tab === 'mutations' ? 'bg-blue-700 text-white font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                    class="px-3.5 py-1.5 text-xs rounded-lg transition-colors flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <span>Ledger Mutasi Stok</span>
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl font-medium flex items-center space-x-2">
        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 p-3.5 bg-red-50 border border-red-200 text-red-800 text-xs rounded-xl font-medium">
        {{ session('error') }}
    </div>
    @endif

    <!-- TAB 1: PENERIMAAN BARANG (PURCHASE ORDER) -->
    <div x-show="tab === 'receipt'" class="space-y-6">
        <!-- Form Penerimaan PO Baru -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6"
             x-data="{
                items: [
                    { product_id: '', quantity: 10, buy_price: 0 }
                ],
                productsMap: {
                    @foreach($products as $p)
                        '{{ $p->id }}': { name: '{{ addslashes($p->name) }}', buy_price: {{ $p->buy_price }}, unit: '{{ $p->unit?->symbol }}' },
                    @endforeach
                },
                addItem() {
                    this.items.push({ product_id: '', quantity: 1, buy_price: 0 });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                onProductChange(index) {
                    const pid = this.items[index].product_id;
                    if (this.productsMap[pid]) {
                        this.items[index].buy_price = this.productsMap[pid].buy_price;
                    }
                },
                get totalSum() {
                    return this.items.reduce((acc, it) => acc + (it.quantity * it.buy_price), 0);
                }
             }">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                <div>
                    <h2 class="text-base font-bold text-slate-800">Input Penerimaan Barang Masuk (Faktur / PO)</h2>
                    <p class="text-xs text-slate-500">Pencatatan barang dari Distribution Center (DC) atau Supplier ke toko</p>
                </div>
            </div>

            <form action="{{ route('warehouse.po.store') }}" method="POST" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Supplier / DC</label>
                        <select name="supplier_id" required class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <option value="">-- Pilih Pemasok --</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->code }} &bull; {{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">No. Surat Jalan / PO</label>
                        <input type="text" 
                               name="po_number" 
                               required 
                               placeholder="Contoh: PO-IDM-{{ date('Ymd') }}-01"
                               value="PO-IDM-{{ date('Ymd') }}-{{ rand(100, 999) }}"
                               class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">No. Faktur Asli Supplier (Opsional)</label>
                        <input type="text" 
                               name="invoice_number" 
                               placeholder="Contoh: INV-SUP-2026-99" 
                               class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                </div>

                <!-- Items Row -->
                <div class="mt-4">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Daftar Barang Diterima</label>
                    <div class="space-y-2">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="flex items-center space-x-2 bg-slate-50 p-2 rounded-xl border border-slate-200">
                                <div class="w-1/2">
                                    <select :name="`items[${index}][product_id]`" 
                                            x-model="item.product_id"
                                            @change="onProductChange(index)"
                                            required 
                                            class="w-full text-xs py-2 px-2.5 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">-- Pilih Barang Retail --</option>
                                        @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->barcode }} &bull; {{ $p->name }} (Stok saat ini: {{ $p->stock }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="w-1/6">
                                    <input type="number" 
                                           :name="`items[${index}][quantity]`" 
                                           x-model.number="item.quantity"
                                           placeholder="Qty" 
                                           min="1" 
                                           required 
                                           class="w-full text-xs py-2 px-2 bg-white border border-slate-300 rounded-lg text-center font-bold">
                                </div>

                                <div class="w-1/4">
                                    <input type="number" 
                                           :name="`items[${index}][buy_price]`" 
                                           x-model.number="item.buy_price"
                                           placeholder="Harga Beli (HPP)" 
                                           min="0" 
                                           required 
                                           class="w-full text-xs py-2 px-2 bg-white border border-slate-300 rounded-lg text-right font-bold">
                                </div>

                                <div>
                                    <button type="button" 
                                            @click="removeItem(index)" 
                                            class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-3 flex items-center justify-between">
                        <button type="button" 
                                @click="addItem" 
                                class="text-xs font-bold text-blue-700 hover:text-blue-800 flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Tambah Baris Barang</span>
                        </button>

                        <div class="text-xs font-bold text-slate-700">
                            Estimasi Total Faktur: <span class="text-sm font-extrabold text-blue-700" x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(totalSum)"></span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end">
                    <button type="submit" 
                            class="px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition-colors">
                        Simpan & Tambah Stok Masuk
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Riwayat PO -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-800">Riwayat Penerimaan Barang (Purchase Orders)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">No. PO</th>
                            <th class="py-3 px-4">Supplier</th>
                            <th class="py-3 px-4">Total Nilai</th>
                            <th class="py-3 px-4">Petugas Gudang</th>
                            <th class="py-3 px-4">Tanggal Diterima</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentPOs as $po)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono font-bold text-blue-700">{{ $po->po_number }}</td>
                            <td class="py-3 px-4 font-medium text-slate-800">{{ $po->supplier?->name }}</td>
                            <td class="py-3 px-4 font-extrabold text-slate-900">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $po->user?->name }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $po->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    Diterima
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada data penerimaan barang masuk.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-t border-slate-100">
                {{ $recentPOs->appends(['tab' => 'receipt'])->links() }}
            </div>
        </div>
    </div>

    <!-- TAB 2: STOCK OPNAME / PENYESUAIAN STOK -->
    <div x-show="tab === 'adjustment'" class="space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6"
             x-data="{
                productId: '',
                physicalStock: '',
                currentStock: 0,
                productsMap: {
                    @foreach($products as $p)
                        '{{ $p->id }}': { name: '{{ addslashes($p->name) }}', stock: {{ $p->stock }} },
                    @endforeach
                },
                onSelect() {
                    if (this.productsMap[this.productId]) {
                        this.currentStock = this.productsMap[this.productId].stock;
                        this.physicalStock = this.currentStock;
                    }
                },
                get diff() {
                    return this.physicalStock === '' ? 0 : (parseInt(this.physicalStock) - this.currentStock);
                }
             }">
            <div class="pb-4 border-b border-slate-100 mb-5">
                <h2 class="text-base font-bold text-slate-800">Form Koreksi / Penyesuaian Stok (Stock Adjustment)</h2>
                <p class="text-xs text-slate-500">Gunakan untuk mencatat barang rusak, kadaluarsa, atau selisih opname fisik</p>
            </div>

            <form action="{{ route('warehouse.adjustment.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Produk</label>
                        <select name="product_id" 
                                x-model="productId" 
                                @change="onSelect"
                                required 
                                class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <option value="">-- Pilih Barang yang Disesuaikan --</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->barcode }} &bull; {{ $p->name }} (Stok Sistem: {{ $p->stock }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Penyesuaian</label>
                        <select name="type" required class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <option value="opname">Stock Opname Berkala (Selisih Fisik)</option>
                            <option value="waste">Barang Rusak / Kadaluarsa / Basi (Waste)</option>
                            <option value="correction">Koreksi Salah Input Sistem</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Jumlah Stok Fisik Riil (Hasil Hitung Lapangan)
                        </label>
                        <input type="number" 
                               name="physical_stock" 
                               x-model="physicalStock"
                               min="0" 
                               required 
                               class="w-full text-sm font-bold py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                        <div class="text-[11px] text-slate-500 mt-1">
                            Stok Tercatat di Sistem: <strong x-text="currentStock"></strong> pcs
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Alasan Penyesuaian (Audit Trail)
                        </label>
                        <input type="text" 
                               name="reason" 
                               required 
                               placeholder="Contoh: Kemasan bocor / Selisih opname mingguan" 
                               class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                </div>

                <!-- Difference indicator -->
                <div class="p-3 rounded-xl border flex items-center justify-between text-xs"
                     :class="diff === 0 ? 'bg-slate-50 border-slate-200 text-slate-600' : (diff > 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800')">
                    <span class="font-bold">Selisih Penyesuaian Stok:</span>
                    <span class="font-extrabold text-sm">
                        <span x-text="diff > 0 ? '+' + diff : diff"></span> pcs
                        <span class="text-[10px] font-normal" x-text="diff === 0 ? '(Tidak ada selisih)' : (diff > 0 ? '(Penambahan stok)' : '(Pengurangan stok)')"></span>
                    </span>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" 
                            class="px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition-colors">
                        Simpan Penyesuaian Stok
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Riwayat Penyesuaian -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-800">Riwayat Penyesuaian Stok Terakhir</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">No. Penyesuaian</th>
                            <th class="py-3 px-4">Barang</th>
                            <th class="py-3 px-4">Jenis</th>
                            <th class="py-3 px-4">Sistem vs Fisik</th>
                            <th class="py-3 px-4">Selisih</th>
                            <th class="py-3 px-4">Alasan</th>
                            <th class="py-3 px-4">Petugas</th>
                            <th class="py-3 px-4">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentAdjustments as $adj)
                        @php $item = $adj->items->first(); @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono font-bold text-blue-700">{{ $adj->adjustment_number }}</td>
                            <td class="py-3 px-4 font-medium text-slate-800">{{ $item?->product?->name ?? '-' }}</td>
                            <td class="py-3 px-4 uppercase text-[10px] font-bold">{{ $adj->type }}</td>
                            <td class="py-3 px-4">{{ $item?->system_stock }} &rarr; {{ $item?->physical_stock }}</td>
                            <td class="py-3 px-4 font-bold {{ ($item?->difference ?? 0) < 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                {{ ($item?->difference ?? 0) > 0 ? '+' : '' }}{{ $item?->difference ?? 0 }}
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $adj->reason }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $adj->user?->name }}</td>
                            <td class="py-3 px-4 text-slate-400">{{ $adj->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">Belum ada data penyesuaian stok.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-t border-slate-100">
                {{ $recentAdjustments->appends(['tab' => 'adjustment'])->links() }}
            </div>
        </div>
    </div>

    <!-- TAB 3: BUKU BESAR MUTASI STOK (AUDIT TRAIL) -->
    <div x-show="tab === 'mutations'" class="space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Buku Besar Audit Trail Mutasi Stok (In & Out Ledger)</h3>
                    <p class="text-xs text-slate-500">Merekam setiap pergerakan stok: Penjualan POS Kasir, Penerimaan PO Gudang, dan Opname.</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Waktu</th>
                            <th class="py-3 px-4">Nama Produk</th>
                            <th class="py-3 px-4">Tipe Arah</th>
                            <th class="py-3 px-4">Jumlah</th>
                            <th class="py-3 px-4">Stok Sebelum</th>
                            <th class="py-3 px-4">Stok Sesudah</th>
                            <th class="py-3 px-4">Keterangan / Referensi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentMutations as $mut)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 text-slate-500 font-mono">{{ $mut->created_at->format('d/m/y H:i:s') }}</td>
                            <td class="py-3 px-4 font-bold text-slate-800">{{ $mut->product?->name }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $mut->type === 'in' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $mut->type === 'in' ? 'MASUK (+)' : 'KELUAR (-)' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-black {{ $mut->type === 'in' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $mut->type === 'in' ? '+' : '-' }}{{ $mut->quantity }}
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $mut->stock_before }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $mut->stock_after }}</td>
                            <td class="py-3 px-4 text-slate-600 truncate max-w-xs">{{ $mut->description }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada mutasi stok tercatat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-t border-slate-100">
                {{ $recentMutations->appends(['tab' => 'mutations'])->links() }}
            </div>
        </div>
    </div>

</div>
@endsection
