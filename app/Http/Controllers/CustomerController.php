<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage_customers');
    }

    /**
     * Display a listing of the customers.
     */
    public function index(Request $request)
    {
        $query = Customer::withCount('sales');
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Active status filter
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->get('is_active'));
        }
        
        $customers = $query->orderBy('name')->paginate(20);
        
        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'credit_limit' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['credit_limit'] = $data['credit_limit'] ?? 0;
        $data['current_balance'] = 0;
        
        $customer = Customer::create($data);
        
        // Log activity
        UserActivityLog::logActivity(
            Auth::user(),
            'customer_created',
            "Created customer: {$customer->name}",
            $customer
        );
        
        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        $customer->load(['sales' => function($query) {
            $query->latest()->limit(10);
        }]);
        
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'credit_limit' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['credit_limit'] = $data['credit_limit'] ?? 0;
        $oldData = $customer->toArray();
        
        $customer->update($data);
        
        // Log activity
        UserActivityLog::logActivity(
            Auth::user(),
            'customer_updated',
            "Updated customer: {$customer->name}",
            $customer,
            $oldData,
            $customer->toArray()
        );
        
        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer)
    {
        // Check if customer has sales
        if ($customer->sales()->exists()) {
            return redirect()->route('customers.index')
                ->with('error', 'Cannot delete customer with existing sales records.');
        }
        
        $customerName = $customer->name;
        $customer->delete();
        
        // Log activity
        UserActivityLog::logActivity(
            Auth::user(),
            'customer_deleted',
            "Deleted customer: {$customerName}"
        );
        
        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    /**
     * Search customers for API calls.
     */
    public function search(Request $request)
    {
        $query = Customer::where('is_active', true);
        
        if ($request->filled('q')) {
            $search = $request->get('q');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $customers = $query->orderBy('name')->limit(20)->get();
        
        return response()->json($customers);
    }
} 