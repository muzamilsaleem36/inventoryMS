<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'todaySales' => $this->getTodaySales(),
            'totalProducts' => $this->getTotalProducts(),
            'totalCustomers' => $this->getTotalCustomers(),
            'lowStockCount' => $this->getLowStockCount(),
            'recentSales' => $this->getRecentSales(),
            'lowStockProducts' => $this->getLowStockProducts(),
            'salesChartLabels' => $this->getSalesChartLabels(),
            'salesChartData' => $this->getSalesChartData(),
            'categoryChartLabels' => $this->getCategoryChartLabels(),
            'categoryChartData' => $this->getCategoryChartData(),
        ];

        return view('dashboard', $data);
    }

    private function getTodaySales()
    {
        return Sale::whereDate('created_at', Carbon::today())
            ->sum('total_amount');
    }

    private function getTotalProducts()
    {
        return Product::count();
    }

    private function getTotalCustomers()
    {
        return Customer::count();
    }

    private function getLowStockCount()
    {
        $threshold = 10; // You can make this configurable
        return Product::where('stock_quantity', '<=', $threshold)
            ->where('stock_quantity', '>', 0)
            ->count();
    }

    private function getRecentSales()
    {
        return Sale::with('customer')
            ->latest()
            ->limit(10)
            ->get();
    }

    private function getLowStockProducts()
    {
        $threshold = 10; // You can make this configurable
        return Product::with('category')
            ->where('stock_quantity', '<=', $threshold)
            ->where('stock_quantity', '>', 0)
            ->orderBy('stock_quantity', 'asc')
            ->limit(5)
            ->get();
    }

    private function getSalesChartLabels()
    {
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $labels[] = Carbon::now()->subDays($i)->format('M j');
        }
        return $labels;
    }

    private function getSalesChartData()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $sales = Sale::whereDate('created_at', $date)
                ->sum('total_amount');
            $data[] = (float) $sales;
        }
        return $data;
    }

    private function getCategoryChartLabels()
    {
        $categories = Category::withCount('products')
            ->having('products_count', '>', 0)
            ->orderBy('products_count', 'desc')
            ->limit(6)
            ->get();

        if ($categories->isEmpty()) {
            return ['No Categories'];
        }

        return $categories->pluck('name')->toArray();
    }

    private function getCategoryChartData()
    {
        $categories = Category::withCount('products')
            ->having('products_count', '>', 0)
            ->orderBy('products_count', 'desc')
            ->limit(6)
            ->get();

        if ($categories->isEmpty()) {
            return [1];
        }

        return $categories->pluck('products_count')->toArray();
    }
} 