<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_reports');
    }

    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $customerId = $request->get('customer_id');
        $paymentMethod = $request->get('payment_method');

        $query = Sale::with(['customer', 'user', 'items.product'])
            ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        $sales = $query->orderBy('created_at', 'desc')->get();

        // Calculate totals
        $totalSales = $sales->sum('total');
        $totalItems = $sales->sum(function ($sale) {
            return $sale->items->sum('quantity');
        });
        $avgSale = $sales->count() > 0 ? $totalSales / $sales->count() : 0;

        // Group by date for chart
        $dailySales = $sales->groupBy(function ($sale) {
            return $sale->created_at->format('Y-m-d');
        })->map(function ($group) {
            return [
                'date' => $group->first()->created_at->format('Y-m-d'),
                'total' => $group->sum('total'),
                'count' => $group->count()
            ];
        })->values();

        // Top products
        $topProducts = Product::withCount(['saleItems' => function ($query) use ($startDate, $endDate) {
            $query->whereHas('sale', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);
            });
        }])
        ->with(['saleItems' => function ($query) use ($startDate, $endDate) {
            $query->whereHas('sale', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);
            });
        }])
        ->orderBy('sale_items_count', 'desc')
        ->limit(10)
        ->get();

        // Payment methods breakdown
        $paymentMethods = $sales->groupBy('payment_method')->map(function ($group) {
            return [
                'method' => $group->first()->payment_method,
                'count' => $group->count(),
                'total' => $group->sum('total')
            ];
        })->values();

        $customers = Customer::all();

        return view('reports.sales', compact(
            'sales', 'totalSales', 'totalItems', 'avgSale', 'dailySales', 
            'topProducts', 'paymentMethods', 'customers', 'startDate', 'endDate'
        ));
    }

    public function products(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $categoryId = $request->get('category_id');
        $status = $request->get('status');

        $query = Product::with(['category', 'saleItems.sale'])
            ->withCount(['saleItems' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('sale', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);
                });
            }])
            ->withSum(['saleItems' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('sale', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);
                });
            }], 'quantity');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($status === 'low_stock') {
            $query->where('stock', '<=', 10);
        } elseif ($status === 'out_of_stock') {
            $query->where('stock', 0);
        }

        $products = $query->orderBy('sale_items_sum_quantity', 'desc')->get();

        // Calculate metrics
        $totalProducts = $products->count();
        $lowStockProducts = $products->where('stock', '<=', 10)->count();
        $outOfStockProducts = $products->where('stock', 0)->count();
        $totalValue = $products->sum(function ($product) {
            return $product->stock * $product->cost_price;
        });

        // Category breakdown
        $categoryBreakdown = $products->groupBy('category.name')->map(function ($group) {
            return [
                'category' => $group->first()->category->name ?? 'Uncategorized',
                'count' => $group->count(),
                'total_stock' => $group->sum('stock'),
                'total_value' => $group->sum(function ($product) {
                    return $product->stock * $product->cost_price;
                })
            ];
        })->values();

        $categories = \App\Models\Category::all();

        return view('reports.products', compact(
            'products', 'totalProducts', 'lowStockProducts', 'outOfStockProducts',
            'totalValue', 'categoryBreakdown', 'categories', 'startDate', 'endDate'
        ));
    }

    public function customers(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $customers = Customer::with(['sales' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);
        }])
        ->withCount(['sales' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);
        }])
        ->withSum(['sales' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);
        }], 'total')
        ->orderBy('sales_sum_total', 'desc')
        ->get();

        // Calculate metrics
        $totalCustomers = $customers->count();
        $activeCustomers = $customers->where('sales_count', '>', 0)->count();
        $totalRevenue = $customers->sum('sales_sum_total');
        $avgOrderValue = $customers->where('sales_count', '>', 0)->avg('sales_sum_total');

        // Top customers
        $topCustomers = $customers->take(10);

        return view('reports.customers', compact(
            'customers', 'totalCustomers', 'activeCustomers', 'totalRevenue',
            'avgOrderValue', 'topCustomers', 'startDate', 'endDate'
        ));
    }

    public function stock(Request $request)
    {
        $categoryId = $request->get('category_id');
        $status = $request->get('status', 'all');

        $query = Product::with('category');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($status === 'low_stock') {
            $query->where('stock', '<=', 10);
        } elseif ($status === 'out_of_stock') {
            $query->where('stock', 0);
        } elseif ($status === 'in_stock') {
            $query->where('stock', '>', 0);
        }

        $products = $query->orderBy('stock', 'asc')->get();

        // Stock value by category
        $categoryStock = $products->groupBy('category.name')->map(function ($group) {
            return [
                'category' => $group->first()->category->name ?? 'Uncategorized',
                'products' => $group->count(),
                'total_stock' => $group->sum('stock'),
                'total_value' => $group->sum(function ($product) {
                    return $product->stock * $product->cost_price;
                })
            ];
        })->values();

        // Stock alerts
        $lowStockProducts = Product::where('stock', '<=', 10)->where('stock', '>', 0)->count();
        $outOfStockProducts = Product::where('stock', 0)->count();
        $totalStockValue = $products->sum(function ($product) {
            return $product->stock * $product->cost_price;
        });

        $categories = \App\Models\Category::all();

        return view('reports.stock', compact(
            'products', 'categoryStock', 'lowStockProducts', 'outOfStockProducts',
            'totalStockValue', 'categories', 'status'
        ));
    }

    public function expenses(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $category = $request->get('category');

        $query = Expense::whereBetween('expense_date', [$startDate, $endDate]);

        if ($category) {
            $query->where('category', $category);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();

        // Calculate totals
        $totalExpenses = $expenses->sum('amount');
        $avgExpense = $expenses->count() > 0 ? $totalExpenses / $expenses->count() : 0;

        // Group by category
        $categoryBreakdown = $expenses->groupBy('category')->map(function ($group) {
            return [
                'category' => $group->first()->category,
                'count' => $group->count(),
                'total' => $group->sum('amount')
            ];
        })->values();

        // Group by date for chart
        $dailyExpenses = $expenses->groupBy(function ($expense) {
            return $expense->expense_date->format('Y-m-d');
        })->map(function ($group) {
            return [
                'date' => $group->first()->expense_date->format('Y-m-d'),
                'total' => $group->sum('amount'),
                'count' => $group->count()
            ];
        })->values();

        $categories = ['Office Supplies', 'Marketing', 'Utilities', 'Rent', 'Equipment', 'Other'];

        return view('reports.expenses', compact(
            'expenses', 'totalExpenses', 'avgExpense', 'categoryBreakdown',
            'dailyExpenses', 'categories', 'startDate', 'endDate'
        ));
    }

    public function dashboard(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period)->startOfDay();
        $endDate = now()->endOfDay();

        // Sales metrics
        $totalSales = Sale::whereBetween('created_at', [$startDate, $endDate])->sum('total');
        $totalOrders = Sale::whereBetween('created_at', [$startDate, $endDate])->count();
        $avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // Product metrics
        $totalProducts = Product::count();
        $lowStockProducts = Product::where('stock', '<=', 10)->count();
        $outOfStockProducts = Product::where('stock', 0)->count();

        // Customer metrics
        $totalCustomers = Customer::count();
        $newCustomers = Customer::whereBetween('created_at', [$startDate, $endDate])->count();

        // Top selling products
        $topProducts = Product::withCount(['saleItems' => function ($query) use ($startDate, $endDate) {
            $query->whereHas('sale', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            });
        }])
        ->orderBy('sale_items_count', 'desc')
        ->limit(5)
        ->get();

        // Recent sales
        $recentSales = Sale::with(['customer', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('reports.dashboard', compact(
            'totalSales', 'totalOrders', 'avgOrderValue', 'totalProducts',
            'lowStockProducts', 'outOfStockProducts', 'totalCustomers',
            'newCustomers', 'topProducts', 'recentSales', 'period'
        ));
    }

    public function exportPdf($type, Request $request)
    {
        $data = $this->getReportData($type, $request);
        
        $pdf = Pdf::loadView("reports.pdf.{$type}", $data);
        
        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'report_exported',
            'description' => "Exported {$type} report as PDF",
            'ip_address' => request()->ip()
        ]);
        
        return $pdf->download("{$type}-report-" . date('Y-m-d') . ".pdf");
    }

    private function getReportData($type, $request)
    {
        switch ($type) {
            case 'sales':
                return $this->getSalesData($request);
            case 'products':
                return $this->getProductsData($request);
            case 'customers':
                return $this->getCustomersData($request);
            case 'stock':
                return $this->getStockData($request);
            case 'expenses':
                return $this->getExpensesData($request);
            default:
                return [];
        }
    }

    private function getSalesData($request)
    {
        // Similar to sales method but return data array
        return [];
    }

    private function getProductsData($request)
    {
        // Similar to products method but return data array
        return [];
    }

    private function getCustomersData($request)
    {
        // Similar to customers method but return data array
        return [];
    }

    private function getStockData($request)
    {
        // Similar to stock method but return data array
        return [];
    }

    private function getExpensesData($request)
    {
        // Similar to expenses method but return data array
        return [];
    }
} 