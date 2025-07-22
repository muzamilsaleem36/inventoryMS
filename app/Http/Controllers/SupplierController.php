<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage_suppliers');
    }

    /**
     * Display a listing of the suppliers.
     */
    public function index(Request $request)
    {
        $query = Supplier::withCount('purchases');
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Active status filter
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->get('is_active'));
        }
        
        $suppliers = $query->orderBy('name')->paginate(20);
        
        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new supplier.
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created supplier in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:suppliers,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'tax_number' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['credit_limit'] = $data['credit_limit'] ?? 0;
        $data['current_balance'] = 0;
        
        $supplier = Supplier::create($data);
        
        // Log activity
        UserActivityLog::logActivity(
            Auth::user(),
            'supplier_created',
            "Created supplier: {$supplier->name} ({$supplier->company_name})",
            $supplier
        );
        
        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    /**
     * Display the specified supplier.
     */
    public function show(Supplier $supplier)
    {
        $supplier->load(['purchases' => function($query) {
            $query->latest()->limit(10);
        }]);
        
        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified supplier.
     */
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified supplier in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:suppliers,email,' . $supplier->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'tax_number' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['credit_limit'] = $data['credit_limit'] ?? 0;
        $oldData = $supplier->toArray();
        
        $supplier->update($data);
        
        // Log activity
        UserActivityLog::logActivity(
            Auth::user(),
            'supplier_updated',
            "Updated supplier: {$supplier->name} ({$supplier->company_name})",
            $supplier,
            $oldData,
            $supplier->toArray()
        );
        
        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    /**
     * Remove the specified supplier from storage.
     */
    public function destroy(Supplier $supplier)
    {
        // Check if supplier has purchases
        if ($supplier->purchases()->exists()) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Cannot delete supplier with existing purchase records.');
        }
        
        $supplierName = $supplier->name;
        $supplier->delete();
        
        // Log activity
        UserActivityLog::logActivity(
            Auth::user(),
            'supplier_deleted',
            "Deleted supplier: {$supplierName}"
        );
        
        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    /**
     * Search suppliers for API calls.
     */
    public function search(Request $request)
    {
        $query = Supplier::where('is_active', true);
        
        if ($request->filled('q')) {
            $search = $request->get('q');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        $suppliers = $query->orderBy('name')->limit(20)->get();
        
        return response()->json($suppliers);
    }
} 