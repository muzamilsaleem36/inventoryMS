<?php

namespace App\Http\Controllers;


use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Setting;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with(['customer', 'user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::where('stock', '>', 0)->get();
        $settings = Setting::pluck('value', 'key');
        
        return view('sales.create', compact('customers', 'products', 'settings'));
    }

    public function pos()
    {
        $customers = Customer::all();
        $products = Product::where('stock', '>', 0)->get();
        $categories = \App\Models\Category::all();
        $settings = Setting::pluck('value', 'key');
        
        return view('sales.pos', compact('customers', 'products', 'categories', 'settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'payment_method' => 'required|in:cash,card,transfer',
            'notes' => 'nullable|string|max:500'
        ]);

        DB::transaction(function () use ($request) {
            // Calculate totals
            $subtotal = 0;
            $items_data = [];
            
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                // Check stock availability
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }
                
                $line_total = $item['quantity'] * $item['price'];
                $subtotal += $line_total;
                
                $items_data[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $line_total
                ];
            }

            // Apply discount
            $discount_amount = 0;
            if ($request->discount_value) {
                if ($request->discount_type === 'percentage') {
                    $discount_amount = ($subtotal * $request->discount_value) / 100;
                } else {
                    $discount_amount = $request->discount_value;
                }
            }

            $discounted_total = $subtotal - $discount_amount;
            
            // Apply tax
            $tax_amount = 0;
            if ($request->tax_rate) {
                $tax_amount = ($discounted_total * $request->tax_rate) / 100;
            }

            $final_total = $discounted_total + $tax_amount;

            // Create sale
            $sale = Sale::create([
                'sale_number' => $this->generateSaleNumber(),
                'customer_id' => $request->customer_id,
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value ?: 0,
                'discount_amount' => $discount_amount,
                'tax_rate' => $request->tax_rate ?: 0,
                'tax_amount' => $tax_amount,
                'total' => $final_total,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'status' => 'completed'
            ]);

            // Create sale items and update stock
            foreach ($items_data as $item_data) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item_data['product_id'],
                    'quantity' => $item_data['quantity'],
                    'price' => $item_data['price'],
                    'total' => $item_data['total']
                ]);

                // Update product stock
                Product::where('id', $item_data['product_id'])
                    ->decrement('stock', $item_data['quantity']);
            }

            // Log activity
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'sale_created',
                'description' => "Created sale #{$sale->sale_number} for " . number_format($final_total, 2),
                'ip_address' => request()->ip()
            ]);

            $this->sale = $sale;
        });

        return redirect()->route('sales.receipt', $this->sale->id)
            ->with('success', 'Sale completed successfully!');
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'user', 'items.product']);
        return view('sales.show', compact('sale'));
    }

    public function receipt(Sale $sale)
    {
        $sale->load(['customer', 'user', 'items.product']);
        $settings = Setting::pluck('value', 'key');
        
        return view('sales.receipt', compact('sale', 'settings'));
    }

    public function printReceipt(Sale $sale)
    {
        $sale->load(['customer', 'user', 'items.product']);
        $settings = Setting::pluck('value', 'key');
        
        $pdf = Pdf::loadView('sales.receipt-pdf', compact('sale', 'settings'));
        return $pdf->stream("receipt-{$sale->sale_number}.pdf");
    }

    public function destroy(Sale $sale)
    {
        // Only allow deletion if sale is not completed or user is admin
        if ($sale->status === 'completed' && !Auth::user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Cannot delete completed sales.');
        }

        DB::transaction(function () use ($sale) {
            // Restore stock for each item
            foreach ($sale->items as $item) {
                Product::where('id', $item->product_id)
                    ->increment('stock', $item->quantity);
            }

            // Log activity
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'sale_deleted',
                'description' => "Deleted sale #{$sale->sale_number}",
                'ip_address' => request()->ip()
            ]);

            $sale->delete();
        });

        return redirect()->route('sales.index')
            ->with('success', 'Sale deleted successfully.');
    }

    public function getProduct($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        
        return response()->json($product);
    }

    public function searchProducts(Request $request)
    {
        $query = $request->get('q');
        $products = Product::where('name', 'like', "%{$query}%")
            ->orWhere('sku', 'like', "%{$query}%")
            ->where('stock', '>', 0)
            ->limit(10)
            ->get();
        
        return response()->json($products);
    }

    private function generateSaleNumber()
    {
        $lastSale = Sale::whereDate('created_at', today())->latest()->first();
        $count = $lastSale ? (int)substr($lastSale->sale_number, -4) + 1 : 1;
        return 'SALE-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    private $sale;
} 