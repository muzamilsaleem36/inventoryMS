@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">Dashboard</h1>
                <div class="page-options">
                    <span class="text-muted">Welcome back, {{ Auth::user()->name }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title">Today's Sales</h3>
                            <h2 class="mb-0">${{ number_format($stats['today_sales'], 2) }}</h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stat-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title">This Month</h3>
                            <h2 class="mb-0">${{ number_format($stats['this_month_sales'], 2) }}</h2>
                            <small class="text-light">
                                @if($stats['sales_growth'] > 0)
                                    <i class="fas fa-arrow-up"></i> {{ $stats['sales_growth'] }}%
                                @elseif($stats['sales_growth'] < 0)
                                    <i class="fas fa-arrow-down"></i> {{ abs($stats['sales_growth']) }}%
                                @else
                                    <i class="fas fa-minus"></i> 0%
                                @endif
                            </small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title">Total Products</h3>
                            <h2 class="mb-0">{{ number_format($stats['total_products']) }}</h2>
                            <small class="text-light">
                                <i class="fas fa-exclamation-triangle"></i> 
                                {{ $stats['low_stock_count'] }} low stock
                            </small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stat-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title">Total Customers</h3>
                            <h2 class="mb-0">{{ number_format($stats['total_customers']) }}</h2>
                            <small class="text-light">
                                <i class="fas fa-user-plus"></i> 
                                {{ $stats['new_customers_this_month'] }} new this month
                            </small>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    @if(auth()->user()->can('manage_expenses'))
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stat-card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title">Monthly Expenses</h3>
                            <h2 class="mb-0">${{ number_format($stats['this_month_expenses'], 2) }}</h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card stat-card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title">Monthly Purchases</h3>
                            <h2 class="mb-0">${{ number_format($stats['this_month_purchases'], 2) }}</h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card stat-card bg-dark text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title">Net Profit</h3>
                            <h2 class="mb-0">${{ number_format($stats['net_profit'], 2) }}</h2>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Content Row -->
    <div class="row">
        <!-- Recent Sales -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Sales</h5>
                    <a href="{{ route('sales.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentSales->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Sale #</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentSales as $sale)
                                    <tr>
                                        <td>{{ $sale->sale_number }}</td>
                                        <td>{{ $sale->customer->name ?? 'Walk-in Customer' }}</td>
                                        <td>${{ number_format($sale->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $sale->status == 'completed' ? 'success' : 'warning' }}">
                                                {{ ucfirst($sale->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $sale->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">No recent sales found.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Low Stock Alert</h5>
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-warning">View All</a>
                </div>
                <div class="card-body">
                    @if($lowStockProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Stock</th>
                                        <th>Min Level</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockProducts as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->category->name }}</td>
                                        <td>{{ $product->stock_quantity }}</td>
                                        <td>{{ $product->min_stock_level }}</td>
                                        <td>
                                            <span class="badge badge-{{ $product->stock_quantity <= 0 ? 'danger' : 'warning' }}">
                                                {{ $product->stock_quantity <= 0 ? 'Out of Stock' : 'Low Stock' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-success mb-0">All products are well stocked!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row -->
    <div class="row">
        <!-- Top Selling Products -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Top Selling Products (This Month)</h5>
                </div>
                <div class="card-body">
                    @if($topProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Sales Count</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topProducts as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->category->name }}</td>
                                        <td>{{ $product->sale_items_count }}</td>
                                        <td>${{ number_format($product->selling_price, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">No sales data available for this month.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        @if(auth()->user()->can('view_activity_logs'))
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Activities</h5>
                </div>
                <div class="card-body">
                    @if($recentActivities->count() > 0)
                        <div class="activity-list">
                            @foreach($recentActivities as $activity)
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="activity-content">
                                    <strong>{{ $activity->user->name }}</strong>
                                    <span class="text-muted">{{ $activity->description }}</span>
                                    <small class="text-muted d-block">{{ $activity->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No recent activities found.</p>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-refresh dashboard stats every 5 minutes
    setInterval(function() {
        fetch('{{ route("api.dashboard.stats") }}')
            .then(response => response.json())
            .then(data => {
                // Update statistics in real-time
                console.log('Dashboard stats updated:', data);
            })
            .catch(error => {
                console.error('Error updating dashboard stats:', error);
            });
    }, 300000); // 5 minutes
</script>
@endsection 