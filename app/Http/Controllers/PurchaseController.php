<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage_purchases');
    }

    public function index()
    {
        $purchases = Purchase::with(['supplier', 'user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        
        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'expected_date' => 'nullable|date|after:today'
        ]);

        DB::transaction(function () use ($request) {
            // Calculate total
            $total = 0;
            $items_data = [];
            
            foreach ($request->items as $item) {
                $line_total = $item['quantity'] * $item['cost_price'];
                $total += $line_total;
                
                $items_data[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'cost_price' => $item['cost_price'],
                    'total' => $line_total
                ];
            }

            // Create purchase
            $purchase = Purchase::create([
                'purchase_number' => $this->generatePurchaseNumber(),
                'supplier_id' => $request->supplier_id,
                'user_id' => Auth::id(),
                'total' => $total,
                'notes' => $request->notes,
                'expected_date' => $request->expected_date,
                'status' => 'pending'
            ]);

            // Create purchase items
            foreach ($items_data as $item_data) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item_data['product_id'],
                    'quantity' => $item_data['quantity'],
                    'cost_price' => $item_data['cost_price'],
                    'total' => $item_data['total']
                ]);
            }

            // Log activity
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'purchase_created',
                'description' => "Created purchase order #{$purchase->purchase_number} for " . number_format($total, 2),
                'ip_address' => request()->ip()
            ]);

            $this->purchase = $purchase;
        });

        return redirect()->route('purchases.show', $this->purchase->id)
            ->with('success', 'Purchase order created successfully!');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'user', 'items.product']);
        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        if ($purchase->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending purchases can be edited.');
        }

        $suppliers = Supplier::all();
        $products = Product::all();
        $purchase->load(['supplier', 'items.product']);
        
        return view('purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        if ($purchase->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending purchases can be updated.');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'expected_date' => 'nullable|date|after:today'
        ]);

        DB::transaction(function () use ($request, $purchase) {
            // Delete existing items
            $purchase->items()->delete();

            // Calculate new total
            $total = 0;
            $items_data = [];
            
            foreach ($request->items as $item) {
                $line_total = $item['quantity'] * $item['cost_price'];
                $total += $line_total;
                
                $items_data[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'cost_price' => $item['cost_price'],
                    'total' => $line_total
                ];
            }

            // Update purchase
            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'total' => $total,
                'notes' => $request->notes,
                'expected_date' => $request->expected_date
            ]);

            // Create new items
            foreach ($items_data as $item_data) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item_data['product_id'],
                    'quantity' => $item_data['quantity'],
                    'cost_price' => $item_data['cost_price'],
                    'total' => $item_data['total']
                ]);
            }

            // Log activity
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'purchase_updated',
                'description' => "Updated purchase order #{$purchase->purchase_number}",
                'ip_address' => request()->ip()
            ]);
        });

        return redirect()->route('purchases.show', $purchase->id)
            ->with('success', 'Purchase order updated successfully!');
    }

    public function receive(Purchase $purchase)
    {
        if ($purchase->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending purchases can be received.');
        }

        $purchase->load(['supplier', 'items.product']);
        return view('purchases.receive', compact('purchase'));
    }

    public function processReceive(Request $request, Purchase $purchase)
    {
        if ($purchase->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending purchases can be received.');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.received_quantity' => 'required|integer|min:0',
            'items.*.actual_cost' => 'nullable|numeric|min:0',
            'received_date' => 'required|date|before_or_equal:today'
        ]);

        DB::transaction(function () use ($request, $purchase) {
            $total_received = 0;
            $all_received = true;

            foreach ($request->items as $item_id => $item_data) {
                $purchaseItem = PurchaseItem::find($item_id);
                if (!$purchaseItem) continue;

                $received_quantity = $item_data['received_quantity'];
                $actual_cost = $item_data['actual_cost'] ?? $purchaseItem->cost_price;

                // Update purchase item
                $purchaseItem->update([
                    'received_quantity' => $received_quantity,
                    'actual_cost' => $actual_cost
                ]);

                // Update product stock and cost
                if ($received_quantity > 0) {
                    $product = $purchaseItem->product;
                    $product->increment('stock', $received_quantity);
                    $product->update(['cost_price' => $actual_cost]);
                    
                    $total_received += $received_quantity * $actual_cost;
                }

                // Check if fully received
                if ($received_quantity < $purchaseItem->quantity) {
                    $all_received = false;
                }
            }

            // Update purchase status
            $purchase->update([
                'status' => $all_received ? 'completed' : 'partial',
                'received_date' => $request->received_date,
                'received_total' => $total_received
            ]);

            // Log activity
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'purchase_received',
                'description' => "Received purchase order #{$purchase->purchase_number} - Status: " . ucfirst($purchase->status),
                'ip_address' => request()->ip()
            ]);
        });

        return redirect()->route('purchases.show', $purchase->id)
            ->with('success', 'Purchase received successfully!');
    }

    public function destroy(Purchase $purchase)
    {
        if ($purchase->status === 'completed') {
            return redirect()->back()->with('error', 'Cannot delete completed purchases.');
        }

        DB::transaction(function () use ($purchase) {
            // If partially received, restore stock
            if ($purchase->status === 'partial') {
                foreach ($purchase->items as $item) {
                    if ($item->received_quantity > 0) {
                        Product::where('id', $item->product_id)
                            ->decrement('stock', $item->received_quantity);
                    }
                }
            }

            // Log activity
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'purchase_deleted',
                'description' => "Deleted purchase order #{$purchase->purchase_number}",
                'ip_address' => request()->ip()
            ]);

            $purchase->delete();
        });

        return redirect()->route('purchases.index')
            ->with('success', 'Purchase deleted successfully.');
    }

    public function print(Purchase $purchase)
    {
        $purchase->load(['supplier', 'user', 'items.product']);
        
        $pdf = Pdf::loadView('purchases.print', compact('purchase'));
        return $pdf->stream("purchase-{$purchase->purchase_number}.pdf");
    }

    private function generatePurchaseNumber()
    {
        $lastPurchase = Purchase::whereDate('created_at', today())->latest()->first();
        $count = $lastPurchase ? (int)substr($lastPurchase->purchase_number, -4) + 1 : 1;
        return 'PO-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    private $purchase;
} 