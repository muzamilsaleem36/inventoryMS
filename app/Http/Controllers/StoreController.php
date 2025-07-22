<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $query = Store::withCount(['users', 'products', 'sales']);
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->get('is_active'));
        }
        
        $stores = $query->orderBy('name')->paginate(20);
        
        return view('stores.index', compact('stores'));
    }

    public function create()
    {
        return view('stores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:stores,code',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
            'opening_hours' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $store = Store::create($request->all());

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'store_created',
            'description' => "Created store: {$store->name} ({$store->code})",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('stores.index')
            ->with('success', 'Store created successfully.');
    }

    public function show(Store $store)
    {
        $store->load(['users', 'products', 'sales']);
        
        // Get store statistics
        $stats = [
            'total_users' => $store->users->count(),
            'total_products' => $store->products->count(),
            'total_sales' => $store->sales->count(),
            'total_revenue' => $store->sales->sum('total'),
            'avg_sale' => $store->sales->avg('total') ?: 0,
            'active_users' => $store->users->where('is_active', true)->count(),
            'low_stock_products' => $store->products->where('stock', '<=', 10)->count()
        ];
        
        return view('stores.show', compact('store', 'stats'));
    }

    public function edit(Store $store)
    {
        return view('stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:stores,code,' . $store->id,
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
            'opening_hours' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $store->update($request->all());

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'store_updated',
            'description' => "Updated store: {$store->name} ({$store->code})",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('stores.index')
            ->with('success', 'Store updated successfully.');
    }

    public function destroy(Store $store)
    {
        // Check if store has users
        if ($store->users()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete store with assigned users.');
        }
        
        // Check if store has products
        if ($store->products()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete store with existing products.');
        }
        
        // Check if store has sales
        if ($store->sales()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete store with sales history.');
        }

        $storeName = $store->name;
        $storeCode = $store->code;

        $store->delete();

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'store_deleted',
            'description' => "Deleted store: {$storeName} ({$storeCode})",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('stores.index')
            ->with('success', 'Store deleted successfully.');
    }

    public function toggleStatus(Store $store)
    {
        $store->update(['is_active' => !$store->is_active]);

        $status = $store->is_active ? 'activated' : 'deactivated';

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'store_status_changed',
            'description' => "Store {$status}: {$store->name} ({$store->code})",
            'ip_address' => request()->ip()
        ]);

        return redirect()->back()
            ->with('success', "Store {$status} successfully.");
    }

    public function dashboard(Store $store)
    {
        $store->load(['users', 'products', 'sales']);
        
        // Get recent sales for this store
        $recentSales = $store->sales()
            ->with(['customer', 'user'])
            ->latest()
            ->limit(10)
            ->get();
        
        // Get low stock products for this store
        $lowStockProducts = $store->products()
            ->where('stock', '<=', 10)
            ->limit(10)
            ->get();
        
        // Get top selling products for this store (last 30 days)
        $topProducts = $store->products()
            ->withCount(['saleItems' => function($query) {
                $query->whereHas('sale', function($q) {
                    $q->where('created_at', '>=', now()->subDays(30));
                });
            }])
            ->orderBy('sale_items_count', 'desc')
            ->limit(5)
            ->get();
        
        // Calculate monthly stats
        $monthlyStats = [
            'sales_count' => $store->sales()->whereMonth('created_at', now()->month)->count(),
            'sales_total' => $store->sales()->whereMonth('created_at', now()->month)->sum('total'),
            'new_customers' => $store->sales()
                ->whereMonth('created_at', now()->month)
                ->distinct('customer_id')
                ->count('customer_id')
        ];
        
        return view('stores.dashboard', compact(
            'store', 'recentSales', 'lowStockProducts', 
            'topProducts', 'monthlyStats'
        ));
    }

    public function report(Store $store, Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        $sales = $store->sales()
            ->with(['customer', 'user', 'items.product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        
        $stats = [
            'total_sales' => $sales->sum('total'),
            'total_orders' => $sales->count(),
            'avg_order_value' => $sales->avg('total') ?: 0,
            'total_items' => $sales->sum(function($sale) {
                return $sale->items->sum('quantity');
            }),
            'unique_customers' => $sales->pluck('customer_id')->unique()->count()
        ];
        
        return view('stores.report', compact('store', 'sales', 'stats', 'startDate', 'endDate'));
    }

    public function transferProducts(Request $request, Store $fromStore)
    {
        $request->validate([
            'to_store_id' => 'required|exists:stores,id|different:' . $fromStore->id,
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id'
        ]);

        $toStore = Store::find($request->to_store_id);
        $products = $fromStore->products()->whereIn('id', $request->product_ids)->get();

        foreach ($products as $product) {
            $product->update(['store_id' => $toStore->id]);
        }

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'products_transferred',
            'description' => "Transferred " . count($products) . " products from {$fromStore->name} to {$toStore->name}",
            'ip_address' => request()->ip()
        ]);

        return redirect()->back()
            ->with('success', 'Products transferred successfully.');
    }
} 