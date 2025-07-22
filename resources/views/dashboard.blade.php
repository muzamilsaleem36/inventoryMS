@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="fas fa-tachometer-alt text-primary-gradient"></i> Dashboard
    </h1>
    <div class="text-muted">
        <i class="fas fa-calendar-alt"></i> {{ now()->format('F j, Y') }}
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon bg-primary-gradient">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Today's Sales
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ App\Helpers\BrandHelper::formatCurrency($todaySales ?? 0) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Total Products
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $totalProducts ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        Total Customers
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $totalCustomers ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                        Low Stock Items
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $lowStockCount ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-xl-8 col-lg-7">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-chart-line"></i> Sales Overview (Last 7 Days)
                </h6>
            </div>
            <div class="card-body">
                <canvas id="salesChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-chart-pie"></i> Top Categories
                </h6>
            </div>
            <div class="card-body">
                <canvas id="categoryChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities & Quick Actions -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-history"></i> Recent Sales
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales ?? [] as $sale)
                            <tr>
                                <td>
                                    <a href="{{ route('sales.show', $sale->id) }}" class="text-decoration-none">
                                        #{{ $sale->invoice_number }}
                                    </a>
                                </td>
                                <td>{{ $sale->customer->name ?? 'Walk-in Customer' }}</td>
                                <td class="font-weight-bold">
                                    {{ App\Helpers\BrandHelper::formatCurrency($sale->total_amount) }}
                                </td>
                                <td>{{ $sale->created_at->format('M j, Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-success">Completed</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    <i class="fas fa-inbox"></i> No recent sales found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-bolt"></i> Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('sales.pos') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-cash-register"></i> New Sale
                    </a>
                    <a href="{{ route('products.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add Product
                    </a>
                    <a href="{{ route('customers.create') }}" class="btn btn-info">
                        <i class="fas fa-user-plus"></i> Add Customer
                    </a>
                    <a href="{{ route('reports.sales') }}" class="btn btn-warning">
                        <i class="fas fa-chart-bar"></i> View Reports
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-exclamation-triangle"></i> Low Stock Alerts
                </h6>
            </div>
            <div class="card-body">
                @forelse($lowStockProducts ?? [] as $product)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong>{{ $product->name }}</strong><br>
                        <small class="text-muted">{{ $product->category->name ?? 'No Category' }}</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-danger">{{ $product->stock_quantity ?? 0 }} left</span>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">
                    <i class="fas fa-check-circle"></i> All products are well stocked
                </p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- System Information -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-info-circle"></i> System Information
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Shop Name:</strong></td>
                                <td>{{ App\Helpers\BrandHelper::getShopName() }}</td>
                            </tr>
                            <tr>
                                <td><strong>Currency:</strong></td>
                                <td>{{ App\Helpers\BrandHelper::getCurrency() }} ({{ App\Helpers\BrandHelper::getCurrencySymbol() }})</td>
                            </tr>
                            <tr>
                                <td><strong>Tax Rate:</strong></td>
                                <td>{{ App\Helpers\BrandHelper::getTaxRate() }}%</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>System Version:</strong></td>
                                <td>{{ App\Helpers\BrandHelper::getSystemVersion() }}</td>
                            </tr>
                            <tr>
                                <td><strong>Developer:</strong></td>
                                <td>{{ App\Helpers\BrandHelper::getCompanyName() }}</td>
                            </tr>
                            <tr>
                                <td><strong>Last Login:</strong></td>
                                <td>{{ auth()->user()->last_login_at ? auth()->user()->last_login_at->format('M j, Y H:i') : 'First login' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: @json($salesChartLabels ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']),
            datasets: [{
                label: 'Sales Amount',
                data: @json($salesChartData ?? [0, 0, 0, 0, 0, 0, 0]),
                borderColor: 'rgb(102, 126, 234)',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '{{ App\Helpers\BrandHelper::getCurrencySymbol() }}' + value;
                        }
                    }
                }
            }
        }
    });

    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: @json($categoryChartLabels ?? ['No Data']),
            datasets: [{
                data: @json($categoryChartData ?? [1]),
                backgroundColor: [
                    '#667eea',
                    '#764ba2',
                    '#28a745',
                    '#ffc107',
                    '#dc3545',
                    '#17a2b8'
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