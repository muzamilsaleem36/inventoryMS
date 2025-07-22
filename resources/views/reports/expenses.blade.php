@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Expenses Report</h3>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-sm" onclick="window.print()">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <a href="{{ route('reports.expenses.export', ['format' => 'pdf']) }}" class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                        <a href="{{ route('reports.expenses.export', ['format' => 'excel']) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('reports.expenses') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ request('start_date', $startDate) }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="{{ request('end_date', $endDate) }}">
                            </div>
                            <div class="col-md-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="store_id" class="form-label">Store</label>
                                <select class="form-select" id="store_id" name="store_id">
                                    <option value="">All Stores</option>
                                    @foreach($stores as $store)
                                    <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>
                                        {{ $store->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method">
                                    <option value="">All Methods</option>
                                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                                    <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="mobile_money" {{ request('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="user_id" class="form-label">User</label>
                                <select class="form-select" id="user_id" name="user_id">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-filter"></i> Apply Filters
                                    </button>
                                    <a href="{{ route('reports.expenses') }}" class="btn btn-secondary">
                                        <i class="fas fa-refresh"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>${{ number_format($totalExpenses, 2) }}</h4>
                                            <p class="mb-0">Total Expenses</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-dollar-sign fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ number_format($totalCount) }}</h4>
                                            <p class="mb-0">Total Records</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-receipt fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>${{ number_format($averageExpense, 2) }}</h4>
                                            <p class="mb-0">Average Expense</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-chart-line fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>${{ number_format($thisMonthExpenses, 2) }}</h4>
                                            <p class="mb-0">This Month</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-calendar-alt fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Monthly Expenses Trend</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="monthlyExpensesChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Expenses by Category</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="categoryExpensesChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Breakdown -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Expenses by Category</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Category</th>
                                                    <th class="text-center">Count</th>
                                                    <th class="text-right">Total Amount</th>
                                                    <th class="text-right">Average</th>
                                                    <th class="text-center">Percentage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($categoryBreakdown as $category)
                                                <tr>
                                                    <td>{{ $categories[$category->category] ?? $category->category }}</td>
                                                    <td class="text-center">{{ $category->count }}</td>
                                                    <td class="text-right">${{ number_format($category->total, 2) }}</td>
                                                    <td class="text-right">${{ number_format($category->average, 2) }}</td>
                                                    <td class="text-center">{{ number_format($category->percentage, 1) }}%</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Breakdown -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Payment Methods</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="paymentMethodChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Top 5 Highest Expenses</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Title</th>
                                                    <th>Category</th>
                                                    <th class="text-right">Amount</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($topExpenses as $expense)
                                                <tr>
                                                    <td>{{ $expense->title }}</td>
                                                    <td>{{ $categories[$expense->category] ?? $expense->category }}</td>
                                                    <td class="text-right">${{ number_format($expense->amount, 2) }}</td>
                                                    <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Expenses List -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Detailed Expenses</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th class="text-right">Amount</th>
                                            <th>Payment Method</th>
                                            <th>User</th>
                                            <th>Store</th>
                                            <th class="text-center">Receipt</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($expenses as $expense)
                                        <tr>
                                            <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                                            <td>{{ $expense->title }}</td>
                                            <td>{{ $categories[$expense->category] ?? $expense->category }}</td>
                                            <td class="text-right">${{ number_format($expense->amount, 2) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $expense->payment_method == 'cash' ? 'success' : 'primary' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}
                                                </span>
                                            </td>
                                            <td>{{ $expense->user->name }}</td>
                                            <td>{{ $expense->store->name ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                @if($expense->receipt_image)
                                                    <i class="fas fa-check text-success"></i>
                                                @else
                                                    <i class="fas fa-times text-danger"></i>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <div>
                                    Showing {{ $expenses->firstItem() }} to {{ $expenses->lastItem() }} of {{ $expenses->total() }} expenses
                                </div>
                                <div>
                                    {{ $expenses->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Monthly Expenses Chart
const monthlyExpensesData = @json($monthlyExpenses);
const ctx1 = document.getElementById('monthlyExpensesChart').getContext('2d');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: monthlyExpensesData.map(item => item.month),
        datasets: [{
            label: 'Monthly Expenses ($)',
            data: monthlyExpensesData.map(item => item.total),
            borderColor: 'rgb(220, 53, 69)',
            backgroundColor: 'rgba(220, 53, 69, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toFixed(2);
                    }
                }
            }
        }
    }
});

// Category Expenses Chart
const categoryExpensesData = @json($categoryExpenses);
const ctx2 = document.getElementById('categoryExpensesChart').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: categoryExpensesData.map(item => item.category),
        datasets: [{
            data: categoryExpensesData.map(item => item.total),
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(255, 159, 64, 0.8)',
                'rgba(199, 199, 199, 0.8)',
                'rgba(83, 102, 255, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': $' + context.parsed.toFixed(2);
                    }
                }
            }
        }
    }
});

// Payment Method Chart
const paymentMethodData = @json($paymentMethodBreakdown);
const ctx3 = document.getElementById('paymentMethodChart').getContext('2d');
new Chart(ctx3, {
    type: 'bar',
    data: {
        labels: paymentMethodData.map(item => item.method),
        datasets: [{
            label: 'Amount ($)',
            data: paymentMethodData.map(item => item.total),
            backgroundColor: 'rgba(75, 192, 192, 0.8)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toFixed(2);
                    }
                }
            }
        }
    }
});
</script>
@endsection 