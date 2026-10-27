<?php

namespace App\Http\Controllers;

use App\Models\CashierShift;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StoreSetting;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Financial KPIs
        $todaySales = Sale::where('status', 'completed')->whereDate('created_at', $today);
        $todayRevenue = (float) $todaySales->sum('total_amount');
        $todayCost = (float) $todaySales->sum('total_cost');
        $todayGrossProfit = $todayRevenue - $todayCost;
        $todayTransactionsCount = $todaySales->count();

        $weekRevenue = (float) Sale::where('status', 'completed')->where('created_at', '>=', $startOfWeek)->sum('total_amount');
        $monthRevenue = (float) Sale::where('status', 'completed')->where('created_at', '>=', $startOfMonth)->sum('total_amount');

        // Top 5 Best Selling Items
        $topSelling = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->with('product.unit')
            ->get();

        // Critical Low Stock Products
        $criticalStock = Product::lowStock()->with(['category', 'unit'])->orderBy('stock')->take(8)->get();

        // 7-day revenue trend data
        $revenueTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $rev = (float) Sale::where('status', 'completed')->whereDate('created_at', $date)->sum('total_amount');
            $revenueTrend[] = [
                'day' => $date->isoFormat('ddd'),
                'date' => $date->format('d/m'),
                'revenue' => $rev,
            ];
        }

        // Recent shift audits
        $recentShifts = CashierShift::with('user')->latest('started_at')->take(5)->get();

        return view('admin.dashboard', [
            'todayRevenue' => $todayRevenue,
            'todayGrossProfit' => $todayGrossProfit,
            'todayTransactionsCount' => $todayTransactionsCount,
            'weekRevenue' => $weekRevenue,
            'monthRevenue' => $monthRevenue,
            'topSelling' => $topSelling,
            'criticalStock' => $criticalStock,
            'revenueTrend' => $revenueTrend,
            'recentShifts' => $recentShifts,
        ]);
    }

    public function reports(Request $request): View
    {
        $startDate = $request->query('start_date', Carbon::today()->subDays(7)->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());
        $cashierId = $request->query('cashier_id');
        $paymentMethod = $request->query('payment_method');

        $query = Sale::with(['user', 'shift', 'items.product'])
            ->where('status', 'completed')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($cashierId) {
            $query->where('user_id', $cashierId);
        }

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        $sales = (clone $query)->latest()->paginate(15);

        // Aggregates for filter
        $totalRevenue = (float) (clone $query)->sum('total_amount');
        $totalCost = (float) (clone $query)->sum('total_cost');
        $totalGrossProfit = $totalRevenue - $totalCost;
        $totalDiscount = (float) (clone $query)->sum('discount_amount');

        $cashiers = User::where('role', 'cashier')->get();

        return view('admin.reports', [
            'sales' => $sales,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'cashierId' => $cashierId,
            'paymentMethod' => $paymentMethod,
            'totalRevenue' => $totalRevenue,
            'totalCost' => $totalCost,
            'totalGrossProfit' => $totalGrossProfit,
            'totalDiscount' => $totalDiscount,
            'cashiers' => $cashiers,
        ]);
    }

    public function products(Request $request): View
    {
        $search = $request->query('q');
        $categoryId = $request->query('category_id');

        $query = Product::with(['category', 'unit'])->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->paginate(15);
        $categories = Category::all();
        $units = Unit::all();

        return view('admin.products', [
            'products' => $products,
            'categories' => $categories,
            'units' => $units,
            'search' => $search,
            'categoryId' => $categoryId,
        ]);
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'barcode' => ['required', 'string', 'max:100', 'unique:products,barcode'],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:200'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'buy_price' => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:1'],
        ]);

        Product::create($validated);

        return redirect()->route('admin.products')->with('success', "Produk {$validated['name']} berhasil ditambahkan!");
    }

    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'barcode' => ['required', 'string', 'max:100', 'unique:products,barcode,' . $product->id],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku,' . $product->id],
            'name' => ['required', 'string', 'max:200'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'buy_price' => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:1'],
        ]);

        $product->update($validated);

        return redirect()->route('admin.products')->with('success', "Data produk {$product->name} berhasil diperbarui!");
    }

    public function settings(): View
    {
        $setting = StoreSetting::current();
        return view('admin.settings', ['setting' => $setting]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:100'],
            'store_code' => ['required', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
            'tax_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'receipt_header' => ['nullable', 'string', 'max:255'],
            'receipt_footer' => ['nullable', 'string', 'max:255'],
            'printer_paper_width' => ['required', 'in:58mm,80mm'],
        ]);

        $setting = StoreSetting::current();
        $setting->update($validated);

        return redirect()->route('admin.settings')->with('success', 'Konfigurasi toko & printer thermal berhasil disimpan!');
    }
}
