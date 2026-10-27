<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\StockMutation;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'receipt');

        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::with(['category', 'unit'])->orderBy('name')->get();

        $recentPOs = PurchaseOrder::with(['supplier', 'user', 'items.product'])
            ->latest()
            ->paginate(10, ['*'], 'po_page');

        $recentAdjustments = StockAdjustment::with(['user', 'items.product'])
            ->latest()
            ->paginate(10, ['*'], 'adj_page');

        $recentMutations = StockMutation::with('product')
            ->latest('created_at')
            ->paginate(15, ['*'], 'mut_page');

        return view('warehouse.index', [
            'tab' => $tab,
            'suppliers' => $suppliers,
            'products' => $products,
            'recentPOs' => $recentPOs,
            'recentAdjustments' => $recentAdjustments,
            'recentMutations' => $recentMutations,
        ]);
    }

    public function storePurchaseOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'po_number' => ['required', 'string', 'max:50', 'unique:purchase_orders,po_number'],
            'invoice_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.buy_price' => ['required', 'numeric', 'min:0'],
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalAmount += ($item['quantity'] * $item['buy_price']);
            }

            $po = PurchaseOrder::create([
                'po_number' => $validated['po_number'],
                'invoice_number' => $validated['invoice_number'] ?? null,
                'supplier_id' => $validated['supplier_id'],
                'user_id' => Auth::id(),
                'total_amount' => $totalAmount,
                'status' => 'received',
                'received_at' => now(),
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $itemData) {
                $subtotal = $itemData['quantity'] * $itemData['buy_price'];

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'buy_price' => $itemData['buy_price'],
                    'subtotal' => $subtotal,
                ]);

                $product = Product::lockForUpdate()->find($itemData['product_id']);
                if ($product) {
                    // Update HPP with newest incoming buy price
                    $product->buy_price = $itemData['buy_price'];

                    StockMutation::record(
                        $product,
                        'purchase_order',
                        $po->id,
                        'in',
                        $itemData['quantity'],
                        "Penerimaan PO {$po->po_number} dari supplier"
                    );
                }
            }

            DB::commit();
            return redirect()->route('warehouse.index', ['tab' => 'receipt'])
                ->with('success', "Penerimaan barang PO {$po->po_number} berhasil dicatat & stok toko bertambah!");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memproses penerimaan barang: ' . $e->getMessage());
        }
    }

    public function storeStockAdjustment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'physical_stock' => ['required', 'integer', 'min:0'],
            'type' => ['required', 'in:correction,opname,waste'],
            'reason' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();
        try {
            $product = Product::lockForUpdate()->find($validated['product_id']);
            $currentStock = $product->stock;
            $physical = (int) $validated['physical_stock'];
            $difference = $physical - $currentStock;

            if ($difference === 0) {
                return back()->with('info', 'Stok fisik sama dengan stok di sistem. Tidak ada perubahan.');
            }

            $adjNumber = 'ADJ-' . date('Ymd') . '-' . rand(1000, 9999);

            $adj = StockAdjustment::create([
                'adjustment_number' => $adjNumber,
                'user_id' => Auth::id(),
                'type' => $validated['type'],
                'reason' => $validated['reason'],
                'notes' => $validated['notes'] ?? null,
            ]);

            StockAdjustmentItem::create([
                'stock_adjustment_id' => $adj->id,
                'product_id' => $product->id,
                'system_stock' => $currentStock,
                'physical_stock' => $physical,
                'difference' => $difference,
            ]);

            $type = $difference > 0 ? 'in' : 'out';
            $qty = abs($difference);

            StockMutation::record(
                $product,
                'stock_adjustment',
                $adj->id,
                $type,
                $qty,
                "Penyesuaian Stok ({$validated['reason']}) {$adjNumber}"
            );

            DB::commit();
            return redirect()->route('warehouse.index', ['tab' => 'adjustment'])
                ->with('success', "Penyesuaian stok {$product->name} berhasil disimpan ({$adjNumber})!");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan penyesuaian stok: ' . $e->getMessage());
        }
    }
}
