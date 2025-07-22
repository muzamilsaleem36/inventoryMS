<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Store;
use App\Models\UserActivityLog;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage_products');
    }

    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'store']);
        
        // Filter by store if user is not admin
        if (!Auth::user()->hasRole('admin') && Auth::user()->store_id) {
            $query->where('store_id', Auth::user()->store_id);
        }
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhereHas('category', function($subq) use ($search) {
                      $subq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }
        
        // Stock status filter
        if ($request->filled('stock_status')) {
            $status = $request->get('stock_status');
            switch ($status) {
                case 'low_stock':
                    $query->where('track_stock', true)
                          ->whereColumn('stock_quantity', '<=', 'min_stock_level');
                    break;
                case 'out_of_stock':
                    $query->where('track_stock', true)
                          ->where('stock_quantity', '<=', 0);
                    break;
                case 'in_stock':
                    $query->where('track_stock', true)
                          ->whereColumn('stock_quantity', '>', 'min_stock_level');
                    break;
            }
        }
        
        // Active status filter
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->get('is_active'));
        }
        
        $products = $query->orderBy('name')->paginate(20);
        $categories = Category::active()->orderBy('name')->get();
        $stores = Store::active()->orderBy('name')->get();
        
        return view('products.index', compact('products', 'categories', 'stores'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        $stores = Store::active()->orderBy('name')->get();
        
        return view('products.create', compact('categories', 'stores'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('products', $imageName, 'public');
            $data['image'] = $imageName;
        }
        
        // Generate barcode if not provided
        if (empty($data['barcode'])) {
            $data['barcode'] = $this->generateBarcode();
        }
        
        // Set store_id if user is not admin
        if (!Auth::user()->hasRole('admin') && Auth::user()->store_id) {
            $data['store_id'] = Auth::user()->store_id;
        }
        
        $product = Product::create($data);
        
        // Log activity
        UserActivityLog::logActivity(
            Auth::user(),
            'product_created',
            "Created product: {$product->name}",
            $product
        );
        
        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'store', 'saleItems.sale', 'purchaseItems.purchase']);
        
        // Check if user can view this product
        if (!Auth::user()->hasRole('admin') && Auth::user()->store_id !== $product->store_id) {
            abort(403, 'Unauthorized access to this product.');
        }
        
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        // Check if user can edit this product
        if (!Auth::user()->hasRole('admin') && Auth::user()->store_id !== $product->store_id) {
            abort(403, 'Unauthorized access to this product.');
        }
        
        $categories = Category::active()->orderBy('name')->get();
        $stores = Store::active()->orderBy('name')->get();
        
        return view('products.edit', compact('product', 'categories', 'stores'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        // Check if user can update this product
        if (!Auth::user()->hasRole('admin') && Auth::user()->store_id !== $product->store_id) {
            abort(403, 'Unauthorized access to this product.');
        }
        
        $data = $request->validated();
        $oldData = $product->toArray();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete('products/' . $product->image);
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('products', $imageName, 'public');
            $data['image'] = $imageName;
        }
        
        // Don't allow store_id change if user is not admin
        if (!Auth::user()->hasRole('admin')) {
            unset($data['store_id']);
        }
        
        $product->update($data);
        
        // Log activity
        UserActivityLog::logActivity(
            Auth::user(),
            'product_updated',
            "Updated product: {$product->name}",
            $product,
            $oldData,
            $product->toArray()
        );
        
        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        // Check if user can delete this product
        if (!Auth::user()->hasRole('admin') && Auth::user()->store_id !== $product->store_id) {
            abort(403, 'Unauthorized access to this product.');
        }
        
        // Check if product has sales or purchase records
        if ($product->saleItems()->exists() || $product->purchaseItems()->exists()) {
            return redirect()->route('products.index')
                ->with('error', 'Cannot delete product with existing sales or purchase records.');
        }
        
        // Delete image if exists
        if ($product->image) {
            Storage::disk('public')->delete('products/' . $product->image);
        }
        
        $productName = $product->name;
        $product->delete();
        
        // Log activity
        UserActivityLog::logActivity(
            Auth::user(),
            'product_deleted',
            "Deleted product: {$productName}"
        );
        
        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Adjust stock quantity for a product.
     */
    public function adjustStock(Request $request, Product $product)
    {
        // Check if user can adjust stock for this product
        if (!Auth::user()->hasRole('admin') && Auth::user()->store_id !== $product->store_id) {
            abort(403, 'Unauthorized access to this product.');
        }
        
        $request->validate([
            'adjustment_type' => 'required|in:increase,decrease',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);
        
        $oldQuantity = $product->stock_quantity;
        $newQuantity = $oldQuantity;
        
        if ($request->adjustment_type === 'increase') {
            $newQuantity += $request->quantity;
        } else {
            $newQuantity -= $request->quantity;
            if ($newQuantity < 0) {
                $newQuantity = 0;
            }
        }
        
        $product->update(['stock_quantity' => $newQuantity]);
        
        // Log activity
        UserActivityLog::logActivity(
            Auth::user(),
            'stock_adjusted',
            "Stock adjusted for {$product->name}: {$oldQuantity} → {$newQuantity}. Reason: {$request->reason}",
            $product
        );
        
        return redirect()->route('products.show', $product)
            ->with('success', 'Stock adjusted successfully.');
    }

    /**
     * Search products for API calls.
     */
    public function search(Request $request)
    {
        $query = Product::with('category');
        
        // Filter by store if user is not admin
        if (!Auth::user()->hasRole('admin') && Auth::user()->store_id) {
            $query->where('store_id', Auth::user()->store_id);
        }
        
        if ($request->filled('q')) {
            $search = $request->get('q');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }
        
        $products = $query->where('is_active', true)
                         ->orderBy('name')
                         ->limit(20)
                         ->get();
        
        return response()->json($products);
    }

    /**
     * Generate a unique barcode.
     */
    private function generateBarcode()
    {
        do {
            $barcode = '2' . str_pad(rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
        } while (Product::where('barcode', $barcode)->exists());
        
        return $barcode;
    }
} 