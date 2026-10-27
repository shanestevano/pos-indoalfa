@extends('layouts.app', ['title' => 'Katalog Produk & Master Data &bull; POS Indomaret'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6" x-data="{ addModal: false, editModal: false, activeProduct: {} }">
    
    <!-- Title & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Manajemen Katalog Produk & Master Data</h1>
            <p class="text-xs text-slate-500 mt-1">
                Atur barcode scanner, SKU, harga pokok (HPP), harga jual retail, dan batas stok minimum.
            </p>
        </div>

        <div>
            <button type="button" 
                    @click="addModal = true"
                    class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Produk Baru</span>
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl font-medium">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('admin.products') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" 
                       name="q" 
                       value="{{ $search }}"
                       placeholder="Cari nama barang, nomor barcode scanner, atau SKU..." 
                       class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <div class="w-full sm:w-48">
                <select name="category_id" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition-colors">
                Filter
            </button>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Barcode / SKU</th>
                        <th class="py-3 px-4">Nama Produk</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-right">Modal (HPP)</th>
                        <th class="py-3 px-4 text-right">Harga Jual</th>
                        <th class="py-3 px-4 text-right">Margin Untung</th>
                        <th class="py-3 px-4 text-center">Stok</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $prod)
                    @php
                        $margin = $prod->sell_price - $prod->buy_price;
                        $marginPct = $prod->sell_price > 0 ? round(($margin / $prod->sell_price) * 100) : 0;
                    @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-4">
                            <div class="font-mono font-bold text-blue-700">{{ $prod->barcode }}</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $prod->sku }}</div>
                        </td>
                        <td class="py-3 px-4 font-bold text-slate-800">
                            {{ $prod->name }}
                        </td>
                        <td class="py-3 px-4 text-slate-500">{{ $prod->category?->name }}</td>
                        <td class="py-3 px-4 text-right text-slate-600">Rp {{ number_format($prod->buy_price, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-right font-bold text-slate-900">Rp {{ number_format($prod->sell_price, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-right">
                            <span class="font-bold text-emerald-600">+Rp {{ number_format($margin, 0, ',', '.') }}</span>
                            <span class="text-[10px] text-slate-400 block font-normal">({{ $marginPct }}%)</span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 rounded-full font-bold text-xs {{ $prod->isLowStock() ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-800' }}">
                                {{ $prod->stock }} {{ $prod->unit?->symbol }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <button type="button" 
                                    @click="activeProduct = {{ json_encode($prod) }}; editModal = true"
                                    class="text-blue-700 hover:text-blue-800 font-bold transition-colors">
                                Edit
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400">Tidak ada produk ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-slate-100">
            {{ $products->withQueryString()->links() }}
        </div>
    </div>

    <!-- MODAL TAMBAH PRODUK BARU -->
    <div x-show="addModal" class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 border border-slate-200">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
                <h3 class="text-base font-extrabold text-slate-800">Tambah Produk Baru ke Katalog</h3>
                <button @click="addModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Barcode EAN-13</label>
                        <input type="text" name="barcode" required placeholder="899..." class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kode SKU</label>
                        <input type="text" name="sku" required placeholder="IDM-XXX-001" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Barang Retail</label>
                    <input type="text" name="name" required placeholder="Contoh: Indomie Soto Lamongan 75g" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kategori</label>
                        <select name="category_id" required class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white">
                            @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Satuan Jual</label>
                        <select name="unit_id" required class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white">
                            @foreach($units as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->symbol }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Harga Beli Modal (HPP)</label>
                        <input type="number" name="buy_price" required min="0" placeholder="Rp" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl font-bold focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Harga Jual Kasir</label>
                        <input type="number" name="sell_price" required min="0" placeholder="Rp" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-blue-700 focus:bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Stok Awal Toko</label>
                        <input type="number" name="stock" required min="0" value="50" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Peringatan Min. Stok</label>
                        <input type="number" name="min_stock" required min="1" value="10" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white">
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="addModal = false" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-bold text-slate-600">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs rounded-xl shadow-xs">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT PRODUK -->
    <div x-show="editModal" class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 border border-slate-200">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
                <h3 class="text-base font-extrabold text-slate-800">Edit Data Produk</h3>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form :action="`/admin/products/${activeProduct.id}`" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Barcode EAN-13</label>
                        <input type="text" name="barcode" x-model="activeProduct.barcode" required class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl font-mono focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kode SKU</label>
                        <input type="text" name="sku" x-model="activeProduct.sku" required class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl font-mono focus:bg-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Barang Retail</label>
                    <input type="text" name="name" x-model="activeProduct.name" required class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kategori</label>
                        <select name="category_id" x-model="activeProduct.category_id" required class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white">
                            @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Satuan Jual</label>
                        <select name="unit_id" x-model="activeProduct.unit_id" required class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white">
                            @foreach($units as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->symbol }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Harga Beli Modal (HPP)</label>
                        <input type="number" name="buy_price" x-model="activeProduct.buy_price" required min="0" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl font-bold focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Harga Jual Kasir</label>
                        <input type="number" name="sell_price" x-model="activeProduct.sell_price" required min="0" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl font-bold text-blue-700 focus:bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Stok Fisik</label>
                        <input type="number" name="stock" x-model="activeProduct.stock" required min="0" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Peringatan Min. Stok</label>
                        <input type="number" name="min_stock" x-model="activeProduct.min_stock" required min="1" class="w-full text-xs py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white">
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="editModal = false" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-bold text-slate-600">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs rounded-xl shadow-xs">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
