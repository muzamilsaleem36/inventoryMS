@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Customers Report</h3>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-sm" onclick="window.print()">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <a href="{{ route('reports.customers.export', ['format' => 'pdf']) }}" class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                        <a href="{{ route('reports.customers.export', ['format' => 'excel']) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('reports.customers') }}" class="mb-4">
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
                                <label for="customer_type" class="form-label">Customer Type</label>
                                <select class="form-select" id="customer_type" name="customer_type">
                                    <option value="">All Customers</option>
                                    <option value="active" {{ request('customer_type') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('customer_type') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="with_balance" {{ request('customer_type') == 'with_balance' ? 'selected' : '' }}>With Balance</option>
                                    <option value="top_buyers" {{ request('customer_type') == 'top_buyers' ? 'selected' : '' }}>Top Buyers</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="sort_by" class="form-label">Sort By</label>
                                <select class="form-select" id="sort_by" name="sort_by">
                                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                                    <option value="total_purchases" {{ request('sort_by') == 'total_purchases' ? 'selected' : '' }}>Total Purchases</option>
                                    <option value="last_purchase" {{ request('sort_by') == 'last_purchase' ? 'selected' : '' }}>Last Purchase</option>
                                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date Added</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Apply Filters
                                </button>
                                <a href="{{ route('reports.customers') }}" class="btn btn-secondary">
                                    <i class="fas fa-refresh"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ number_format($totalCustomers) }}</h4>
                                            <p class="mb-0">Total Customers</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-users fa-2x"></i>
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
                                            <h4>{{ number_format($activeCustomers) }}</h4>
                                            <p class="mb-0">Active Customers</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-user-check fa-2x"></i>
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
                                            <h4>{{ number_format($newCustomers) }}</h4>
                                            <p class="mb-0">New Customers</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-user-plus fa-2x"></i>
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
                                            <h4>${{ number_format($totalCustomerValue, 2) }}</h4>
                                            <p class="mb-0">Total Customer Value</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-dollar-sign fa-2x"></i>
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
                                    <h5 class="card-title mb-0">Customer Registration Trend</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="customerTrendChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Customer Status Distribution</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="customerStatusChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Customers -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Top 10 Customers by Purchase Value</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Rank</th>
                                                    <th>Customer</th>
                                                    <th class="text-center">Total Orders</th>
                                                    <th class="text-right">Total Spent</th>
                                                    <th class="text-right">Average Order</th>
                                                    <th class="text-center">Last Purchase</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($topCustomers as $index => $customer)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $customer->name }}</td>
                                                    <td class="text-center">{{ $customer->sales_count }}</td>
                                                    <td class="text-right">${{ number_format($customer->total_spent, 2) }}</td>
                                                    <td class="text-right">${{ number_format($customer->total_spent / max($customer->sales_count, 1), 2) }}</td>
                                                    <td class="text-center">{{ $customer->last_purchase ? $customer->last_purchase->format('M d, Y') : 'Never' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customers List -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Customer Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th class="text-center">Total Orders</th>
                                            <th class="text-right">Total Spent</th>
                                            <th class="text-right">Current Balance</th>
                                            <th class="text-right">Credit Limit</th>
                                            <th class="text-center">Last Purchase</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($customers as $customer)
                                        <tr>
                                            <td>{{ $customer->name }}</td>
                                            <td>{{ $customer->email ?? 'N/A' }}</td>
                                            <td>{{ $customer->phone }}</td>
                                            <td class="text-center">{{ $customer->sales_count }}</td>
                                            <td class="text-right">${{ number_format($customer->total_spent ?? 0, 2) }}</td>
                                            <td class="text-right">${{ number_format($customer->current_balance, 2) }}</td>
                                            <td class="text-right">${{ number_format($customer->credit_limit, 2) }}</td>
                                            <td class="text-center">{{ $customer->last_purchase ? $customer->last_purchase->format('M d, Y') : 'Never' }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-{{ $customer->is_active ? 'success' : 'danger' }}">
                                                    {{ $customer->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <div>
                                    Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} customers
                                </div>
                                <div>
                                    {{ $customers->links() }}
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
// Customer Trend Chart
const customerTrendData = @json($customerTrend);
const ctx1 = document.getElementById('customerTrendChart').getContext('2d');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: customerTrendData.map(item => item.date),
        datasets: [{
            label: 'New Customers',
            data: customerTrendData.map(item => item.count),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Customer Status Chart
const customerStatusData = @json($customerStatus);
const ctx2 = document.getElementById('customerStatusChart').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: customerStatusData.map(item => item.status),
        datasets: [{
            data: customerStatusData.map(item => item.count),
            backgroundColor: [
                'rgba(75, 192, 192, 0.8)',
                'rgba(255, 99, 132, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(54, 162, 235, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endsection 