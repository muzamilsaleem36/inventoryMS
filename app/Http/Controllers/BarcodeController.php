<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;

class BarcodeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|manager');
    }

    public function index(Request $request)
    {
        $query = Product::query();
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }
        
        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }
        
        $products = $query->with('category')->orderBy('name')->paginate(20);
        $categories = \App\Models\Category::all();
        
        return view('barcodes.index', compact('products', 'categories'));
    }

    public function generate(Product $product)
    {
        try {
            $barcode = $product->barcode ?: $this->generateBarcodeNumber();
            
            // Update product with barcode if it doesn't have one
            if (!$product->barcode) {
                $product->update(['barcode' => $barcode]);
            }
            
            $barcodeFormat = \App\Models\Setting::where('key', 'barcode_format')->value('value') ?: 'CODE128';
            
            // Generate barcode image
            $barcodeImage = $this->createBarcodeImage($barcode, $barcodeFormat);
            
            // Log activity
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'barcode_generated',
                'description' => "Generated barcode for product: {$product->name}",
                'ip_address' => request()->ip()
            ]);
            
            return view('barcodes.show', compact('product', 'barcode', 'barcodeImage', 'barcodeFormat'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error generating barcode: ' . $e->getMessage());
        }
    }

    public function batchGenerate(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
            'format' => 'required|in:CODE128,CODE39,EAN13,EAN8,UPC_A,UPC_E'
        ]);

        $products = Product::whereIn('id', $request->product_ids)->get();
        $barcodes = [];
        
        foreach ($products as $product) {
            try {
                $barcode = $product->barcode ?: $this->generateBarcodeNumber();
                
                // Update product with barcode if it doesn't have one
                if (!$product->barcode) {
                    $product->update(['barcode' => $barcode]);
                }
                
                $barcodeImage = $this->createBarcodeImage($barcode, $request->format);
                
                $barcodes[] = [
                    'product' => $product,
                    'barcode' => $barcode,
                    'image' => $barcodeImage
                ];
            } catch (\Exception $e) {
                continue; // Skip products with errors
            }
        }
        
        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'batch_barcodes_generated',
            'description' => "Generated barcodes for " . count($barcodes) . " products",
            'ip_address' => request()->ip()
        ]);
        
        return view('barcodes.batch', compact('barcodes', 'request'));
    }

    public function print(Product $product)
    {
        $barcode = $product->barcode ?: $this->generateBarcodeNumber();
        $barcodeFormat = \App\Models\Setting::where('key', 'barcode_format')->value('value') ?: 'CODE128';
        $barcodeImage = $this->createBarcodeImage($barcode, $barcodeFormat);
        
        return view('barcodes.print', compact('product', 'barcode', 'barcodeImage'));
    }

    public function downloadSvg(Product $product)
    {
        $barcode = $product->barcode ?: $this->generateBarcodeNumber();
        $barcodeFormat = \App\Models\Setting::where('key', 'barcode_format')->value('value') ?: 'CODE128';
        
        try {
            $dns1d = new DNS1D();
            $svg = $dns1d->getBarcodeSVG($barcode, $barcodeFormat, 2, 60);
            
            return response($svg)
                ->header('Content-Type', 'image/svg+xml')
                ->header('Content-Disposition', 'attachment; filename="' . $product->name . '-barcode.svg"');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error generating SVG: ' . $e->getMessage());
        }
    }

    public function downloadPng(Product $product)
    {
        $barcode = $product->barcode ?: $this->generateBarcodeNumber();
        $barcodeFormat = \App\Models\Setting::where('key', 'barcode_format')->value('value') ?: 'CODE128';
        
        try {
            $dns1d = new DNS1D();
            $png = $dns1d->getBarcodePNG($barcode, $barcodeFormat, 3, 60);
            
            return response(base64_decode($png))
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', 'attachment; filename="' . $product->name . '-barcode.png"');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error generating PNG: ' . $e->getMessage());
        }
    }

    public function regenerate(Product $product)
    {
        try {
            $newBarcode = $this->generateBarcodeNumber();
            $product->update(['barcode' => $newBarcode]);
            
            // Log activity
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'barcode_regenerated',
                'description' => "Regenerated barcode for product: {$product->name}",
                'ip_address' => request()->ip()
            ]);
            
            return redirect()->route('barcodes.generate', $product)
                ->with('success', 'Barcode regenerated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error regenerating barcode: ' . $e->getMessage());
        }
    }

    private function createBarcodeImage($barcode, $format = 'CODE128')
    {
        try {
            $dns1d = new DNS1D();
            return $dns1d->getBarcodeSVG($barcode, $format, 2, 60);
        } catch (\Exception $e) {
            // Fallback to PNG if SVG fails
            try {
                return $dns1d->getBarcodePNG($barcode, $format, 3, 60);
            } catch (\Exception $e2) {
                throw new \Exception('Unable to generate barcode: ' . $e2->getMessage());
            }
        }
    }

    private function generateBarcodeNumber()
    {
        do {
            // Generate 12-digit barcode number
            $barcode = str_pad(mt_rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT);
        } while (Product::where('barcode', $barcode)->exists());
        
        return $barcode;
    }

    public function validateBarcode(Request $request)
    {
        $barcode = $request->get('barcode');
        $productId = $request->get('product_id');
        
        $exists = Product::where('barcode', $barcode)
            ->when($productId, function($query) use ($productId) {
                return $query->where('id', '!=', $productId);
            })
            ->exists();
        
        return response()->json(['exists' => $exists]);
    }

    public function bulkPrint(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
            'copies' => 'required|integer|min:1|max:10'
        ]);

        $products = Product::whereIn('id', $request->product_ids)->get();
        $copies = $request->copies;
        $barcodeFormat = \App\Models\Setting::where('key', 'barcode_format')->value('value') ?: 'CODE128';
        
        $printData = [];
        
        foreach ($products as $product) {
            $barcode = $product->barcode ?: $this->generateBarcodeNumber();
            $barcodeImage = $this->createBarcodeImage($barcode, $barcodeFormat);
            
            for ($i = 0; $i < $copies; $i++) {
                $printData[] = [
                    'product' => $product,
                    'barcode' => $barcode,
                    'image' => $barcodeImage
                ];
            }
        }
        
        return view('barcodes.bulk-print', compact('printData'));
    }
} 