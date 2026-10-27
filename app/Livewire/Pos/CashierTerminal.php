<?php

namespace App\Livewire\Pos;

use App\Models\CashierShift;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMutation;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CashierTerminal extends Component
{
    // Shift state
    public ?CashierShift $shift = null;
    public string $startingCashInput = '200000';
    public bool $closeShiftModalOpen = false;
    public string $actualCashInput = '';
    public string $closingNotes = '';
    public ?CashierShift $closedShiftSummary = null;

    // POS State
    public string $barcodeInput = '';
    public string $searchQuery = '';
    public ?int $selectedCategoryId = null;

    // Cart: array of [product_id, name, barcode, price, cost, qty, subtotal]
    public array $cart = [];
    public float $discountAmount = 0;
    public bool $taxEnabled = false;

    // Payment state
    public bool $paymentModalOpen = false;
    public string $paymentMethod = 'cash'; // 'cash', 'qris', 'edc'
    public string $cashReceivedInput = '';
    public string $nonCashReference = '';

    // Receipt print state
    public ?Sale $lastSale = null;
    public bool $receiptModalOpen = false;

    // Store settings
    public StoreSetting $storeSetting;

    public function mount(): void
    {
        $this->storeSetting = StoreSetting::current();
        $this->loadActiveShift();
    }

    public function loadActiveShift(): void
    {
        $user = Auth::user();
        if ($user) {
            $this->shift = CashierShift::where('user_id', $user->id)
                ->where('status', 'open')
                ->latest('started_at')
                ->first();
        }
    }

    public function openShift(): void
    {
        $this->validate([
            'startingCashInput' => ['required', 'numeric', 'min:0'],
        ]);

        $this->shift = CashierShift::create([
            'user_id' => Auth::id(),
            'starting_cash' => (float) $this->startingCashInput,
            'expected_cash' => (float) $this->startingCashInput,
            'started_at' => now(),
            'status' => 'open',
        ]);

        session()->flash('success', 'Shift kasir berhasil dibuka dengan modal awal Rp ' . number_format($this->shift->starting_cash, 0, ',', '.'));
    }

    public function scanBarcode(): void
    {
        $code = trim($this->barcodeInput);
        if ($code === '') {
            return;
        }

        $product = Product::where('is_active', true)
            ->where(function ($q) use ($code) {
                $q->where('barcode', $code)
                  ->orWhere('sku', $code);
            })
            ->first();

        if ($product) {
            $this->addToCart($product->id);
            $this->barcodeInput = '';
        } else {
            $this->dispatch('pos-alert', [
                'type' => 'error',
                'message' => "Barang dengan barcode [{$code}] tidak ditemukan!",
            ]);
            $this->barcodeInput = '';
        }
    }

    public function addToCart(int $productId, int $qty = 1): void
    {
        $product = Product::find($productId);
        if (!$product || !$product->is_active) {
            return;
        }

        if (isset($this->cart[$productId])) {
            $newQty = $this->cart[$productId]['qty'] + $qty;
            if ($newQty > $product->stock) {
                $this->dispatch('pos-alert', [
                    'type' => 'warning',
                    'message' => "Stok produk {$product->name} tersisa {$product->stock} {$product->unit?->symbol}!",
                ]);
            }
            $this->cart[$productId]['qty'] = $newQty;
            $this->cart[$productId]['subtotal'] = $newQty * $this->cart[$productId]['price'];
        } else {
            if ($product->stock < $qty) {
                $this->dispatch('pos-alert', [
                    'type' => 'warning',
                    'message' => "Peringatan: Stok {$product->name} tersisa {$product->stock}!",
                ]);
            }

            $this->cart[$productId] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'barcode' => $product->barcode,
                'unit' => $product->unit?->symbol ?? 'pcs',
                'price' => (float) $product->sell_price,
                'cost' => (float) $product->buy_price,
                'stock' => $product->stock,
                'qty' => $qty,
                'subtotal' => $qty * (float) $product->sell_price,
            ];
        }

        $this->dispatch('cart-updated');
    }

    public function updateQty(int $productId, int $newQty): void
    {
        if ($newQty <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['qty'] = $newQty;
            $this->cart[$productId]['subtotal'] = $newQty * $this->cart[$productId]['price'];
        }
    }

    public function incrementQty(int $productId): void
    {
        if (isset($this->cart[$productId])) {
            $this->updateQty($productId, $this->cart[$productId]['qty'] + 1);
        }
    }

    public function decrementQty(int $productId): void
    {
        if (isset($this->cart[$productId])) {
            $this->updateQty($productId, $this->cart[$productId]['qty'] - 1);
        }
    }

    public function removeFromCart(int $productId): void
    {
        unset($this->cart[$productId]);
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->discountAmount = 0;
    }

    public function getSubtotalProperty(): float
    {
        return array_sum(array_column($this->cart, 'subtotal'));
    }

    public function getTaxAmountProperty(): float
    {
        if (!$this->taxEnabled || $this->storeSetting->tax_percentage <= 0) {
            return 0.0;
        }

        $taxable = max(0, $this->subtotal - $this->discountAmount);
        return round($taxable * ($this->storeSetting->tax_percentage / 100));
    }

    public function getTotalAmountProperty(): float
    {
        return max(0, $this->subtotal - $this->discountAmount + $this->taxAmount);
    }

    public function getTotalCostProperty(): float
    {
        $cost = 0;
        foreach ($this->cart as $item) {
            $cost += ($item['cost'] * $item['qty']);
        }
        return $cost;
    }

    public function openPaymentModal(): void
    {
        if (empty($this->cart)) {
            $this->dispatch('pos-alert', [
                'type' => 'warning',
                'message' => 'Keranjang belanja masih kosong!',
            ]);
            return;
        }

        $this->cashReceivedInput = (string) $this->totalAmount;
        $this->paymentMethod = 'cash';
        $this->paymentModalOpen = true;
    }

    public function closePaymentModal(): void
    {
        $this->paymentModalOpen = false;
    }

    public function selectQuickCash(float $amount): void
    {
        $this->cashReceivedInput = (string) $amount;
    }

    public function getChangeAmountProperty(): float
    {
        $received = (float) $this->cashReceivedInput;
        return max(0, $received - $this->totalAmount);
    }

    public function processPayment(): void
    {
        if (empty($this->cart)) {
            return;
        }

        if (!$this->shift) {
            $this->loadActiveShift();
            if (!$this->shift) {
                $this->dispatch('pos-alert', ['type' => 'error', 'message' => 'Silakan buka shift kasir terlebih dahulu.']);
                return;
            }
        }

        $amountPaid = (float) $this->cashReceivedInput;
        if ($this->paymentMethod === 'cash' && $amountPaid < $this->totalAmount) {
            $this->dispatch('pos-alert', [
                'type' => 'error',
                'message' => 'Jumlah uang tunai kurang dari total belanja!',
            ]);
            return;
        }

        if ($this->paymentMethod !== 'cash') {
            $amountPaid = $this->totalAmount;
        }

        DB::beginTransaction();
        try {
            // Generate Invoice Number IDM-YYYYMMDD-XXXX
            $datePrefix = date('Ymd');
            $countToday = Sale::whereDate('created_at', today())->count() + 1;
            $invoiceNumber = 'IDM-' . $datePrefix . '-' . str_pad((string)$countToday, 4, '0', STR_PAD_LEFT);

            $changeAmount = $this->paymentMethod === 'cash' ? max(0, $amountPaid - $this->totalAmount) : 0;

            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'cashier_shift_id' => $this->shift->id,
                'user_id' => Auth::id(),
                'subtotal' => $this->subtotal,
                'discount_amount' => $this->discountAmount,
                'tax_amount' => $this->taxAmount,
                'total_amount' => $this->totalAmount,
                'total_cost' => $this->totalCost,
                'payment_method' => $this->paymentMethod,
                'amount_paid' => $amountPaid,
                'change_amount' => $changeAmount,
                'status' => 'completed',
                'notes' => $this->paymentMethod !== 'cash' ? ($this->nonCashReference ?: strtoupper($this->paymentMethod)) : null,
            ]);

            foreach ($this->cart as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                if ($product) {
                    $itemSubtotal = $item['subtotal'];
                    $itemProfit = $itemSubtotal - ($item['cost'] * $item['qty']);

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'buy_price' => $item['cost'],
                        'sell_price' => $item['price'],
                        'quantity' => $item['qty'],
                        'discount_amount' => 0,
                        'subtotal' => $itemSubtotal,
                        'profit' => $itemProfit,
                    ]);

                    StockMutation::record(
                        $product,
                        'sale',
                        $sale->id,
                        'out',
                        $item['qty'],
                        "Penjualan Kasir {$invoiceNumber}"
                    );
                }
            }

            // Recalculate shift sales
            $this->shift->recalculateTotals();

            DB::commit();

            $this->lastSale = $sale->load(['items.product', 'user']);
            $this->paymentModalOpen = false;
            $this->receiptModalOpen = true;
            $this->cart = [];
            $this->discountAmount = 0;

            $this->dispatch('play-chime');
            $this->dispatch('pos-alert', [
                'type' => 'success',
                'message' => "Transaksi {$invoiceNumber} berhasil diproses!",
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->dispatch('pos-alert', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ]);
        }
    }

    public function closeReceiptModal(): void
    {
        $this->receiptModalOpen = false;
        $this->dispatch('focus-barcode');
    }

    public function openCloseShiftModal(): void
    {
        if ($this->shift) {
            $this->shift->recalculateTotals();
            $this->actualCashInput = (string) $this->shift->expected_cash;
            $this->closeShiftModalOpen = true;
        }
    }

    public function executeCloseShift(): void
    {
        if (!$this->shift) {
            return;
        }

        $this->validate([
            'actualCashInput' => ['required', 'numeric', 'min:0'],
        ]);

        $actual = (float) $this->actualCashInput;
        $expected = (float) $this->shift->expected_cash;
        $diff = $actual - $expected;

        $this->shift->update([
            'actual_closing_cash' => $actual,
            'difference' => $diff,
            'closed_at' => now(),
            'status' => 'closed',
            'notes' => $this->closingNotes,
        ]);

        $this->closedShiftSummary = $this->shift;
        $this->shift = null;
        $this->closeShiftModalOpen = false;

        $this->dispatch('pos-alert', [
            'type' => 'success',
            'message' => 'Shift kasir resmi ditutup. Rekonsiliasi kas tercatat.',
        ]);
    }

    public function render()
    {
        $categories = Category::all();

        $productsQuery = Product::with(['category', 'unit'])
            ->where('is_active', true);

        if ($this->selectedCategoryId) {
            $productsQuery->where('category_id', $this->selectedCategoryId);
        }

        if (trim($this->searchQuery) !== '') {
            $q = trim($this->searchQuery);
            $productsQuery->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('barcode', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%");
            });
        }

        $products = $productsQuery->orderBy('name')->take(24)->get();

        return view('livewire.pos.cashier-terminal', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}
