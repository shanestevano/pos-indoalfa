<div class="h-[calc(100vh-3.5rem)] flex flex-col bg-slate-100 overflow-hidden select-none"
     x-data="{
        initShortcuts() {
            window.addEventListener('keydown', e => {
                // Ignore shortcuts if in standard typing except barcode
                if (e.key === 'F2') {
                    e.preventDefault();
                    document.getElementById('pos-search-input')?.focus();
                } else if (e.key === 'F8') {
                    e.preventDefault();
                    if (confirm('Bersihkan seluruh keranjang belanja?')) {
                        @this.call('clearCart');
                    }
                } else if (e.key === 'F12' || (e.code === 'Space' && e.ctrlKey)) {
                    e.preventDefault();
                    @this.call('openPaymentModal');
                }
            });
        }
     }"
     x-init="initShortcuts()">

    <!-- POS Sub-Header Status Bar -->
    <div class="bg-slate-900 text-white px-4 py-2 flex items-center justify-between text-xs border-b border-slate-800">
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-1.5">
                <span class="w-2.5 h-2.5 rounded-full {{ $shift ? 'bg-emerald-400 animate-pulse' : 'bg-red-500' }}"></span>
                <span class="font-bold uppercase tracking-wider">
                    {{ $shift ? 'Shift Kasir Aktif' : 'Shift Belum Dibuka' }}
                </span>
            </div>

            @if($shift)
            <div class="hidden sm:flex items-center space-x-3 text-slate-300">
                <span>Modal Awal: <strong class="text-white">Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}</strong></span>
                <span>&bull;</span>
                <span>Omzet Tunai: <strong class="text-emerald-400">Rp {{ number_format($shift->cash_sales_total, 0, ',', '.') }}</strong></span>
                <span>&bull;</span>
                <span>Mulai: <strong class="text-white">{{ $shift->started_at->format('H:i') }} WIB</strong></span>
            </div>
            @endif
        </div>

        <div class="flex items-center space-x-2">
            @if($shift)
            <button wire:click="openCloseShiftModal" 
                    type="button"
                    class="px-3 py-1 bg-red-600/90 hover:bg-red-600 text-white font-bold rounded-md transition-colors flex items-center space-x-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <span>Tutup Shift</span>
            </button>
            @else
            <button wire:click="$set('shift', null)" 
                    type="button"
                    class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-md transition-colors">
                Buka Shift Baru
            </button>
            @endif
        </div>
    </div>

    <!-- Main Workspace (Desktop Split 60% Catalog / 40% Cart) -->
    <div class="flex-1 flex overflow-hidden">
        
        <!-- Left Side: Barcode Scanner & Product Catalog (60%) -->
        <div class="w-7/12 flex flex-col border-r border-slate-200 bg-slate-50 overflow-hidden">
            
            <!-- Barcode Scanner Input Bar -->
            <div class="p-3 bg-white border-b border-slate-200 shadow-xs">
                <form wire:submit.prevent="scanBarcode" class="flex space-x-2">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                        </div>
                        <input type="text" 
                               wire:model="barcodeInput"
                               id="pos-barcode-input"
                               autofocus
                               autocomplete="off"
                               placeholder="SCAN BARCODE DI SINI atau ketik kode EAN-13 (Tekan Enter)..." 
                               class="w-full pl-10 pr-4 py-2.5 bg-blue-50/50 border-2 border-blue-500 rounded-xl text-sm font-mono font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition-all placeholder:text-slate-400 placeholder:font-sans">
                    </div>
                    <button type="submit" 
                            class="px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-xl text-xs flex items-center space-x-1.5 transition-colors shadow-xs">
                        <span>Scan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </form>

                <!-- Search by name & Category pills -->
                <div class="mt-2.5 flex items-center space-x-2">
                    <div class="relative w-1/3">
                        <input type="text" 
                               wire:model.live.debounce.300ms="searchQuery" 
                               id="pos-search-input"
                               placeholder="[F2] Cari nama barang..." 
                               class="w-full pl-8 pr-3 py-1.5 text-xs bg-slate-100 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <!-- Category Pills -->
                    <div class="flex-1 flex items-center space-x-1.5 overflow-x-auto no-scrollbar py-0.5">
                        <button type="button" 
                                wire:click="$set('selectedCategoryId', null)"
                                class="px-2.5 py-1 text-xs font-semibold rounded-lg shrink-0 transition-colors {{ is_null($selectedCategoryId) ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' }}">
                            Semua
                        </button>
                        @foreach($categories as $category)
                        <button type="button" 
                                wire:click="$set('selectedCategoryId', {{ $category->id }})"
                                class="px-2.5 py-1 text-xs font-semibold rounded-lg shrink-0 transition-colors {{ $selectedCategoryId === $category->id ? 'bg-blue-700 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' }}">
                            {{ $category->name }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Product Cards Grid -->
            <div class="flex-1 p-3 overflow-y-auto">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5">
                    @forelse($products as $product)
                    <div wire:click="addToCart({{ $product->id }})"
                         class="bg-white rounded-xl border border-slate-200 p-2.5 hover:border-blue-500 hover:shadow-md transition-all cursor-pointer flex flex-col justify-between group active:scale-[0.98]">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-mono text-slate-400 tracking-tight">{{ $product->barcode }}</span>
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $product->isLowStock() ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">
                                    Stok: {{ $product->stock }}
                                </span>
                            </div>
                            <h3 class="text-xs font-bold text-slate-800 line-clamp-2 group-hover:text-blue-700 leading-snug">
                                {{ $product->name }}
                            </h3>
                        </div>

                        <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[11px] text-slate-400 font-medium">{{ $product->unit?->symbol ?? 'pcs' }}</span>
                            <span class="text-xs font-extrabold text-blue-700">
                                Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-12 text-center text-slate-400 text-xs">
                        Tidak ada produk yang cocok dengan pencarian.
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Keyboard shortcut helper footer -->
            <div class="p-2 bg-white border-t border-slate-200 flex items-center justify-between text-[11px] text-slate-500">
                <div class="flex items-center space-x-3">
                    <span><kbd class="px-1.5 py-0.5 bg-slate-100 border border-slate-300 rounded font-mono text-[10px]">F2</kbd> Cari Barang</span>
                    <span><kbd class="px-1.5 py-0.5 bg-slate-100 border border-slate-300 rounded font-mono text-[10px]">F8</kbd> Batal</span>
                    <span><kbd class="px-1.5 py-0.5 bg-slate-100 border border-slate-300 rounded font-mono text-[10px]">F12</kbd> Bayar</span>
                </div>
                <div>Barcode Scanner: <span class="text-emerald-600 font-bold">Siap (HID Mode)</span></div>
            </div>
        </div>

        <!-- Right Side: POS Cart & Bill Summary (40%) -->
        <div class="w-5/12 flex flex-col bg-white overflow-hidden shadow-lg border-l border-slate-200">
            
            <!-- Cart Header -->
            <div class="p-3 border-b border-slate-200 bg-slate-50/80 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-800">Keranjang Belanja</span>
                    <span class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-blue-100 text-blue-700">
                        {{ count($cart) }} item
                    </span>
                </div>

                @if(count($cart) > 0)
                <button wire:click="clearCart" 
                        type="button" 
                        class="text-xs text-red-600 hover:text-red-700 font-bold transition-colors">
                    Kosongkan (F8)
                </button>
                @endif
            </div>

            <!-- Cart Items List (Scrollable) -->
            <div class="flex-1 p-3 overflow-y-auto divide-y divide-slate-100">
                @forelse($cart as $productId => $item)
                <div class="py-2.5 flex items-center justify-between group">
                    <div class="w-1/2 pr-2">
                        <div class="text-xs font-bold text-slate-800 truncate" title="{{ $item['name'] }}">
                            {{ $item['name'] }}
                        </div>
                        <div class="text-[11px] text-slate-500">
                            @ Rp {{ number_format($item['price'], 0, ',', '.') }}
                        </div>
                    </div>

                    <!-- Quantity Controls -->
                    <div class="flex items-center space-x-1">
                        <button wire:click="decrementQty({{ $productId }})" 
                                type="button"
                                class="w-6 h-6 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center transition-colors">
                            -
                        </button>
                        <input type="number" 
                               wire:change="updateQty({{ $productId }}, $event.target.value)"
                               value="{{ $item['qty'] }}" 
                               min="1"
                               class="w-10 text-center text-xs font-bold py-0.5 border border-slate-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <button wire:click="incrementQty({{ $productId }})" 
                                type="button"
                                class="w-6 h-6 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center transition-colors">
                            +
                        </button>
                    </div>

                    <!-- Subtotal & Remove -->
                    <div class="text-right pl-2 flex items-center space-x-2">
                        <div class="text-xs font-bold text-slate-800">
                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                        </div>
                        <button wire:click="removeFromCart({{ $productId }})" 
                                type="button"
                                class="text-slate-300 hover:text-red-500 transition-colors p-1"
                                title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                @empty
                <div class="h-full flex flex-col items-center justify-center text-center p-6 text-slate-400">
                    <svg class="w-12 h-12 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <p class="text-xs font-medium">Keranjang belanja kosong</p>
                    <p class="text-[11px] text-slate-400 mt-1">Scan barcode barang atau pilih dari katalog di sebelah kiri.</p>
                </div>
                @endforelse
            </div>

            <!-- Bill Totals & Actions -->
            <div class="p-4 bg-slate-50 border-t border-slate-200 space-y-3">
                <div class="space-y-1.5 text-xs text-slate-600">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span class="font-semibold text-slate-800">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="flex items-center space-x-1">
                            <span>Diskon</span>
                        </span>
                        <div class="flex items-center space-x-1">
                            <span class="text-slate-400">Rp</span>
                            <input type="number" 
                                   wire:model.live.debounce.300ms="discountAmount"
                                   min="0"
                                   class="w-20 text-right text-xs py-0.5 px-1.5 border border-slate-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                    </div>

                    @if($storeSetting->tax_percentage > 0)
                    <div class="flex justify-between items-center">
                        <label class="flex items-center space-x-1.5 cursor-pointer">
                            <input type="checkbox" wire:model.live="taxEnabled" class="rounded text-blue-600">
                            <span>PPN ({{ $storeSetting->tax_percentage }}%)</span>
                        </label>
                        <span class="font-semibold text-slate-800">Rp {{ number_format($this->taxAmount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                <!-- Big Grand Total -->
                <div class="pt-2 border-t border-slate-200 flex justify-between items-baseline">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-600">Total Tagihan</span>
                    <span class="text-2xl font-black text-blue-700 tracking-tight">
                        Rp {{ number_format($this->totalAmount, 0, ',', '.') }}
                    </span>
                </div>

                <!-- Checkout Button -->
                <button wire:click="openPaymentModal" 
                        type="button"
                        {{ empty($cart) ? 'disabled' : '' }}
                        class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-300 text-white font-extrabold text-sm rounded-xl shadow-md hover:shadow-lg transition-all duration-150 flex items-center justify-center space-x-2 active:scale-[0.99]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>BAYAR SEKARANG (F12)</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 1. MODAL BUKA SHIFT KASIR (FORCED IF NO ACTIVE SHIFT) -->
    @if(!$shift)
    <div class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-200">
            <div class="text-center mb-5">
                <div class="w-12 h-12 bg-blue-100 text-blue-700 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Pembukaan Shift Kasir</h3>
                <p class="text-xs text-slate-500 mt-1">
                    Silakan masukkan modal laci awal (Cash Drawer Opening) sebelum memulai transaksi kasir.
                </p>
            </div>

            <form wire:submit.prevent="openShift" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Modal Uang Pecahan Awal (Rp)
                    </label>
                    <input type="number" 
                           wire:model="startingCashInput"
                           required
                           min="0"
                           step="1000"
                           class="w-full text-lg font-bold text-blue-700 px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <div class="flex items-center space-x-2 mt-2">
                        <button type="button" wire:click="$set('startingCashInput', '100000')" class="px-2.5 py-1 text-xs bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-700 font-semibold">Rp 100.000</button>
                        <button type="button" wire:click="$set('startingCashInput', '200000')" class="px-2.5 py-1 text-xs bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-700 font-semibold">Rp 200.000</button>
                        <button type="button" wire:click="$set('startingCashInput', '500000')" class="px-2.5 py-1 text-xs bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-700 font-semibold">Rp 500.000</button>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            class="w-full py-2.5 px-4 bg-blue-700 hover:bg-blue-800 text-white font-bold text-sm rounded-xl transition-all shadow-md">
                        Konfirmasi & Buka Kasir
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- 2. MODAL PEMBAYARAN MULTI-METODE (TUNAI / QRIS / EDC) -->
    @if($paymentModalOpen)
    <div class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 border border-slate-200">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <div>
                    <h3 class="text-base font-extrabold text-slate-800">Pembayaran Transaksi</h3>
                    <p class="text-xs text-slate-500">Pilih metode bayar pelanggan</p>
                </div>
                <button wire:click="closePaymentModal" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Total display in modal -->
            <div class="my-4 p-4 rounded-xl bg-blue-50/60 border border-blue-100 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase">Total Yang Harus Dibayar:</span>
                <span class="text-2xl font-black text-blue-700">Rp {{ number_format($this->totalAmount, 0, ',', '.') }}</span>
            </div>

            <!-- Payment Method Tabs -->
            <div class="grid grid-cols-3 gap-2 mb-4">
                <button type="button" 
                        wire:click="$set('paymentMethod', 'cash')"
                        class="p-2.5 rounded-xl border text-center transition-all {{ $paymentMethod === 'cash' ? 'bg-blue-700 text-white border-blue-700 font-bold shadow-xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                    <span class="block text-xs font-bold">Tunai (Cash)</span>
                </button>
                <button type="button" 
                        wire:click="$set('paymentMethod', 'qris')"
                        class="p-2.5 rounded-xl border text-center transition-all {{ $paymentMethod === 'qris' ? 'bg-blue-700 text-white border-blue-700 font-bold shadow-xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                    <span class="block text-xs font-bold">QRIS</span>
                </button>
                <button type="button" 
                        wire:click="$set('paymentMethod', 'edc')"
                        class="p-2.5 rounded-xl border text-center transition-all {{ $paymentMethod === 'edc' ? 'bg-blue-700 text-white border-blue-700 font-bold shadow-xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                    <span class="block text-xs font-bold">EDC / Debit</span>
                </button>
            </div>

            <!-- If Tunai (Cash) -->
            @if($paymentMethod === 'cash')
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Uang Diterima dari Pelanggan (Rp)</label>
                    <input type="number" 
                           wire:model.live="cashReceivedInput"
                           autofocus
                           min="{{ $this->totalAmount }}"
                           class="w-full text-xl font-black text-slate-800 px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>

                <!-- Quick Cash Chips -->
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" wire:click="selectQuickCash({{ $this->totalAmount }})" class="px-2.5 py-1 text-xs bg-slate-100 hover:bg-blue-100 hover:text-blue-700 rounded-lg font-bold text-slate-700">Uang Pas</button>
                    <button type="button" wire:click="selectQuickCash(10000)" class="px-2.5 py-1 text-xs bg-slate-100 hover:bg-blue-100 hover:text-blue-700 rounded-lg font-bold text-slate-700">Rp 10.000</button>
                    <button type="button" wire:click="selectQuickCash(20000)" class="px-2.5 py-1 text-xs bg-slate-100 hover:bg-blue-100 hover:text-blue-700 rounded-lg font-bold text-slate-700">Rp 20.000</button>
                    <button type="button" wire:click="selectQuickCash(50000)" class="px-2.5 py-1 text-xs bg-slate-100 hover:bg-blue-100 hover:text-blue-700 rounded-lg font-bold text-slate-700">Rp 50.000</button>
                    <button type="button" wire:click="selectQuickCash(100000)" class="px-2.5 py-1 text-xs bg-slate-100 hover:bg-blue-100 hover:text-blue-700 rounded-lg font-bold text-slate-700">Rp 100.000</button>
                    <button type="button" wire:click="selectQuickCash(200000)" class="px-2.5 py-1 text-xs bg-slate-100 hover:bg-blue-100 hover:text-blue-700 rounded-lg font-bold text-slate-700">Rp 200.000</button>
                </div>

                <!-- Change Calculation -->
                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-800">Kembalian Uang Tunai:</span>
                    <span class="text-lg font-extrabold text-emerald-700">
                        Rp {{ number_format($this->changeAmount, 0, ',', '.') }}
                    </span>
                </div>
            </div>
            @endif

            <!-- If QRIS -->
            @if($paymentMethod === 'qris')
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-center space-y-3">
                <div class="w-36 h-36 bg-white border border-slate-300 rounded-xl mx-auto flex items-center justify-center p-2 shadow-xs">
                    <!-- Simulated QRIS Graphic -->
                    <svg class="w-full h-full text-slate-800" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm14-2h4v2h-4v-2zm-4 0h2v4h-2v-4zm2 4h2v2h-2v-2zm2 2h4v2h-4v-2zm-6 0h2v2h-2v-2z"/>
                    </svg>
                </div>
                <div class="text-xs text-slate-600 font-medium">
                    Arahkan kamera smartphone pelanggan ke kode QR di atas (GoPay, OVO, Dana, BCA, Livin, dll)
                </div>
            </div>
            @endif

            <!-- If EDC -->
            @if($paymentMethod === 'edc')
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Kartu / Approval Code EDC (Opsional)</label>
                    <input type="text" 
                           wire:model="nonCashReference"
                           placeholder="Contoh: BCA-DEBIT-98239" 
                           class="w-full text-sm px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>
                <p class="text-xs text-slate-500">Gesek atau tap kartu di mesin EDC kasir, lalu klik Proses Transaksi.</p>
            </div>
            @endif

            <!-- Action buttons -->
            <div class="mt-6 flex space-x-2">
                <button type="button" 
                        wire:click="closePaymentModal"
                        class="w-1/3 py-2.5 border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl transition-colors">
                    Batal
                </button>
                <button type="button" 
                        wire:click="processPayment"
                        class="w-2/3 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md transition-colors flex items-center justify-center space-x-1.5">
                    <span>SELESAIKAN & CETAK STRUK</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- 3. MODAL CETAK STRUK BELANJA THERMAL (58MM/80MM) -->
    @if($receiptModalOpen && $lastSale)
    <div class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-5 border border-slate-200">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center space-x-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-xs font-bold text-slate-800">Transaksi Selesai</span>
                </div>
                <button wire:click="closeReceiptModal" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Receipt Container (Thermal Monospace Style) -->
            <div id="thermal-receipt" class="my-3 p-4 bg-slate-50 border border-dashed border-slate-300 rounded-xl font-mono-receipt text-[11px] leading-tight text-slate-900 shadow-inner">
                <div class="text-center font-bold text-xs uppercase mb-1">
                    {{ $storeSetting->store_name }}
                </div>
                <div class="text-center text-[10px] text-slate-600 mb-2">
                    {{ $storeSetting->address }}<br>
                    Telp: {{ $storeSetting->phone }}
                </div>

                <div class="border-t border-b border-dashed border-slate-400 py-1.5 my-1.5 text-[10px] space-y-0.5">
                    <div class="flex justify-between">
                        <span>No: {{ $lastSale->invoice_number }}</span>
                        <span>{{ $lastSale->created_at->format('d/m/y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Kasir: {{ $lastSale->user?->name ?? 'Kasir' }}</span>
                        <span>{{ strtoupper($lastSale->payment_method) }}</span>
                    </div>
                </div>

                <!-- Items -->
                <div class="py-1 space-y-1">
                    @foreach($lastSale->items as $item)
                    <div>
                        <div class="font-bold truncate">{{ $item->product?->name }}</div>
                        <div class="flex justify-between text-[10px] text-slate-700 pl-2">
                            <span>{{ $item->quantity }} x {{ number_format($item->sell_price, 0, ',', '.') }}</span>
                            <span class="font-bold">{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="border-t border-dashed border-slate-400 pt-1.5 mt-1.5 space-y-0.5 text-[10px]">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span>{{ number_format($lastSale->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($lastSale->discount_amount > 0)
                    <div class="flex justify-between">
                        <span>Diskon:</span>
                        <span>-{{ number_format($lastSale->discount_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($lastSale->tax_amount > 0)
                    <div class="flex justify-between">
                        <span>PPN:</span>
                        <span>{{ number_format($lastSale->tax_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-bold text-xs pt-1 border-t border-slate-300">
                        <span>TOTAL:</span>
                        <span>Rp {{ number_format($lastSale->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between pt-0.5">
                        <span>Bayar:</span>
                        <span>{{ number_format($lastSale->amount_paid, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between font-bold">
                        <span>Kembali:</span>
                        <span>{{ number_format($lastSale->change_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="text-center text-[9px] text-slate-500 mt-3 pt-2 border-t border-dashed border-slate-300">
                    {{ $storeSetting->receipt_footer }}<br>
                    Layanan Konsumen: 1500-280
                </div>
            </div>

            <!-- Receipt Actions -->
            <div class="flex space-x-2 pt-1">
                <button type="button" 
                        onclick="window.print()" 
                        class="w-1/2 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl flex items-center justify-center space-x-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span>Cetak Struk</span>
                </button>
                <button type="button" 
                        wire:click="closeReceiptModal" 
                        class="w-1/2 py-2 bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs rounded-xl transition-colors">
                    Transaksi Baru
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- 4. MODAL TUTUP SHIFT KASIR (REKONSILIASI KAS LACI) -->
    @if($closeShiftModalOpen && $shift)
    <div class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-200">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <div>
                    <h3 class="text-base font-extrabold text-slate-800">Tutup Shift Kasir</h3>
                    <p class="text-xs text-slate-500">Rekonsiliasi uang fisik laci vs sistem</p>
                </div>
                <button wire:click="$set('closeShiftModalOpen', false)" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="my-4 space-y-2 text-xs">
                <div class="p-3 bg-slate-50 rounded-xl space-y-1.5 border border-slate-200">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Modal Laci Awal:</span>
                        <span class="font-bold">Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Penjualan Tunai Sistem:</span>
                        <span class="font-bold text-emerald-600">+ Rp {{ number_format($shift->cash_sales_total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Penjualan Non-Tunai (QRIS/EDC):</span>
                        <span class="font-bold text-blue-600">Rp {{ number_format($shift->non_cash_sales_total, 0, ',', '.') }}</span>
                    </div>
                    <div class="pt-1.5 border-t border-slate-200 flex justify-between font-bold text-sm">
                        <span>Uang Seharusnya di Laci:</span>
                        <span class="text-blue-700">Rp {{ number_format($shift->expected_cash, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Hitung Fisik Uang di Laci Kasir (Rp)
                    </label>
                    <input type="number" 
                           wire:model.live="actualCashInput"
                           min="0"
                           class="w-full text-lg font-black text-slate-900 px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>

                @php
                    $diff = ((float)$actualCashInput) - $shift->expected_cash;
                @endphp
                <div class="p-3 rounded-xl border flex items-center justify-between {{ $diff == 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : ($diff > 0 ? 'bg-blue-50 border-blue-200 text-blue-700' : 'bg-red-50 border-red-200 text-red-700') }}">
                    <span class="font-bold">Selisih Kas Laci:</span>
                    <span class="font-black text-sm">
                        {{ $diff > 0 ? '+ ' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                        <span class="text-[10px] font-normal">({{ $diff == 0 ? 'Cocok / Klop' : ($diff > 0 ? 'Lebih' : 'Kurang') }})</span>
                    </span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Penutupan Shift (Opsional)</label>
                    <textarea wire:model="closingNotes" 
                              rows="2" 
                              placeholder="Keterangan jika terdapat selisih uang..."
                              class="w-full text-xs p-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
                </div>
            </div>

            <div class="flex space-x-2">
                <button type="button" 
                        wire:click="$set('closeShiftModalOpen', false)"
                        class="w-1/3 py-2.5 border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl transition-colors">
                    Batal
                </button>
                <button type="button" 
                        wire:click="executeCloseShift"
                        class="w-2/3 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl shadow-md transition-colors">
                    Konfirmasi Tutup Shift
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
