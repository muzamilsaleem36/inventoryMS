<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $query = Expense::with('user');
        
        // Date filter
        if ($request->filled('start_date')) {
            $query->whereDate('expense_date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('expense_date', '<=', $request->end_date);
        }
        
        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%");
            });
        }
        
        $expenses = $query->orderBy('expense_date', 'desc')->paginate(20);
        
        // Calculate totals
        $totalExpenses = $query->sum('amount');
        
        $categories = [
            'office_supplies' => 'Office Supplies',
            'utilities' => 'Utilities',
            'rent' => 'Rent',
            'marketing' => 'Marketing',
            'equipment' => 'Equipment',
            'travel' => 'Travel',
            'meals' => 'Meals & Entertainment',
            'professional_services' => 'Professional Services',
            'insurance' => 'Insurance',
            'maintenance' => 'Maintenance',
            'other' => 'Other'
        ];
        
        return view('expenses.index', compact('expenses', 'totalExpenses', 'categories'));
    }

    public function create()
    {
        $categories = [
            'office_supplies' => 'Office Supplies',
            'utilities' => 'Utilities',
            'rent' => 'Rent',
            'marketing' => 'Marketing',
            'equipment' => 'Equipment',
            'travel' => 'Travel',
            'meals' => 'Meals & Entertainment',
            'professional_services' => 'Professional Services',
            'insurance' => 'Insurance',
            'maintenance' => 'Maintenance',
            'other' => 'Other'
        ];
        
        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:50',
            'expense_date' => 'required|date|before_or_equal:today',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048'
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
            $data['receipt_path'] = $receiptPath;
        }

        $expense = Expense::create($data);

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'expense_created',
            'description' => "Created expense: {$expense->description} - $" . number_format($expense->amount, 2),
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }

    public function show(Expense $expense)
    {
        $expense->load('user');
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $categories = [
            'office_supplies' => 'Office Supplies',
            'utilities' => 'Utilities',
            'rent' => 'Rent',
            'marketing' => 'Marketing',
            'equipment' => 'Equipment',
            'travel' => 'Travel',
            'meals' => 'Meals & Entertainment',
            'professional_services' => 'Professional Services',
            'insurance' => 'Insurance',
            'maintenance' => 'Maintenance',
            'other' => 'Other'
        ];
        
        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:50',
            'expense_date' => 'required|date|before_or_equal:today',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048'
        ]);

        $data = $request->except(['receipt']);

        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            // Delete old receipt if exists
            if ($expense->receipt_path) {
                \Storage::disk('public')->delete($expense->receipt_path);
            }
            
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
            $data['receipt_path'] = $receiptPath;
        }

        $expense->update($data);

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'expense_updated',
            'description' => "Updated expense: {$expense->description}",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        // Delete receipt file if exists
        if ($expense->receipt_path) {
            \Storage::disk('public')->delete($expense->receipt_path);
        }

        $expenseDescription = $expense->description;
        $expense->delete();

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'expense_deleted',
            'description' => "Deleted expense: {$expenseDescription}",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    public function downloadReceipt(Expense $expense)
    {
        if (!$expense->receipt_path || !\Storage::disk('public')->exists($expense->receipt_path)) {
            return redirect()->back()->with('error', 'Receipt not found.');
        }

        return \Storage::disk('public')->download($expense->receipt_path);
    }

    public function report(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date', 'desc')
            ->get();
        
        // Group by category
        $categoryTotals = $expenses->groupBy('category')->map(function ($group) {
            return [
                'total' => $group->sum('amount'),
                'count' => $group->count(),
                'avg' => $group->avg('amount')
            ];
        });
        
        // Group by month
        $monthlyTotals = $expenses->groupBy(function ($expense) {
            return $expense->expense_date->format('Y-m');
        })->map(function ($group) {
            return [
                'total' => $group->sum('amount'),
                'count' => $group->count()
            ];
        });
        
        $totalExpenses = $expenses->sum('amount');
        $avgExpense = $expenses->avg('amount') ?: 0;
        
        return view('expenses.report', compact(
            'expenses', 'categoryTotals', 'monthlyTotals', 
            'totalExpenses', 'avgExpense', 'startDate', 'endDate'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        $expenses = Expense::with('user')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date', 'desc')
            ->get();
        
        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'expenses_exported',
            'description' => "Exported expenses report ({$startDate} to {$endDate})",
            'ip_address' => request()->ip()
        ]);
        
        return \Excel::download(new \App\Exports\ExpensesExport($expenses), 'expenses-' . date('Y-m-d') . '.xlsx');
    }
} 