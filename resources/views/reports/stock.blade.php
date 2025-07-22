@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Stock Report</h3>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-sm" onclick="window.print()">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <a href="{{ route('reports.stock.export', ['format' => 'pdf']) }}" class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                        <a href="{{ route('reports.stock.export', ['format' => 'excel']) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('reports.stock') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
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
                            <div class="col-md-3">
                                <label for="stock_level" class="form-label">Stock Level</label>
                                <select class="form-select" id="stock_level" name="stock_level">
                                    <option value="">All Levels</option>
                                    <option value="out_of_stock" {{ request('stock_level') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                                    <option value="low_stock" {{ request('stock_level') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                                    <option value="normal_stock" {{ request('stock_level') == 'normal_stock' ? 'selected' : '' }}>Normal Stock</option>
                                    <option value="overstock" {{ request('stock_level') == 'overstock' ? 'selected' : '' }}>Overstock</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="value_range" class="form-label">Value Range</label>
                                <select class="form-select" id="value_range" name="value_range">
                                    <option value="">All Values</option>
                                    <option value="low" {{ request('value_range') == 'low' ? 'selected' : '' }}>Low Value (< $100)</option>
                                    <option value="medium" {{ request('value_range') == 'medium' ? 'selected' : '' }}>Medium Value ($100 - $1000)</option>
                                    <option value="high" {{ request('value_range') == 'high' ? 'selected' : '' }}>High Value (> $1000)</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Apply Filters
                                </button>
                                <a href="{{ route('reports.stock') }}" class="btn btn-secondary">
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
                                            <h4>{{ number_format($totalItems) }}</h4>
                                            <p class="mb-0">Total Items</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-box fa-2x"></i>
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
                                            <h4>{{ number_format($totalQuantity) }}</h4>
                                            <p class="mb-0">Total Quantity</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-boxes fa-2x"></i>
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
                                            <h4>${{ number_format($totalValue, 2) }}</h4>
                                            <p class="mb-0">Total Value</p>
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
                                            <h4>{{ number_format($lowStockCount) }}</h4>
                                            <p class="mb-0">Low Stock Items</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Level Distribution -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Stock Level Distribution</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="stockLevelChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Stock Value by Category</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="stockValueChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Critical Stock Alerts -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Critical Stock Alerts</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Out of Stock Items</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Product</th>
                                                            <th>Category</th>
                                                            <th>Store</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($outOfStockItems as $item)
                                                        <tr>
                                                            <td>{{ $item->name }}</td>
                                                            <td>{{ $item->category->name }}</td>
                                                            <td>{{ $item->store->name ?? 'N/A' }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Low Stock Items</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Product</th>
                                                            <th>Current</th>
                                                            <th>Min Level</th>
                                                            <th>Store</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($lowStockItems as $item)
                                                        <tr>
                                                            <td>{{ $item->name }}</td>
                                                            <td>{{ $item->stock_quantity }}</td>
                                                            <td>{{ $item->min_stock_level }}</td>
                                                            <td>{{ $item->store->name ?? 'N/A' }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Stock Report -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Detailed Stock Report</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Product Code</th>
                                            <th>Product Name</th>
                                            <th>Category</th>
                                            <th>Store</th>
                                            <th class="text-center">Current Stock</th>
                                            <th class="text-center">Min Level</th>
                                            <th class="text-center">Max Level</th>
                                            <th class="text-right">Unit Cost</th>
                                            <th class="text-right">Stock Value</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Last Updated</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stockItems as $item)
                                        <tr>
                                            <td>{{ $item->code }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->category->name }}</td>
                                            <td>{{ $item->store->name ?? 'N/A' }}</td>
                                            <td class="text-center">{{ number_format($item->stock_quantity) }}</td>
                                            <td class="text-center">{{ number_format($item->min_stock_level) }}</td>
                                            <td class="text-center">{{ number_format($item->max_stock_level) }}</td>
                                            <td class="text-right">${{ number_format($item->purchase_price, 2) }}</td>
                                            <td class="text-right">${{ number_format($item->stock_quantity * $item->purchase_price, 2) }}</td>
                                            <td class="text-center">
                                                @if($item->stock_quantity == 0)
                                                    <span class="badge badge-danger">Out of Stock</span>
                                                @elseif($item->stock_quantity <= $item->min_stock_level)
                                                    <span class="badge badge-warning">Low Stock</span>
                                                @elseif($item->stock_quantity >= $item->max_stock_level)
                                                    <span class="badge badge-info">Overstock</span>
                                                @else
                                                    <span class="badge badge-success">Normal</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $item->updated_at->format('M d, Y') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <div>
                                    Showing {{ $stockItems->firstItem() }} to {{ $stockItems->lastItem() }} of {{ $stockItems->total() }} items
                                </div>
                                <div>
                                    {{ $stockItems->links() }}
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
// Stock Level Chart
const stockLevelData = @json($stockLevelData);
const ctx1 = document.getElementById('stockLevelChart').getContext('2d');
new Chart(ctx1, {
    type: 'doughnut',
    data: {
        labels: stockLevelData.map(item => item.level),
        datasets: [{
            data: stockLevelData.map(item => item.count),
            backgroundColor: [
                'rgba(220, 53, 69, 0.8)',   // Out of Stock - Red
                'rgba(255, 193, 7, 0.8)',   // Low Stock - Yellow
                'rgba(40, 167, 69, 0.8)',   // Normal - Green
                'rgba(23, 162, 184, 0.8)'   // Overstock - Blue
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

// Stock Value Chart
const stockValueData = @json($stockValueData);
const ctx2 = document.getElementById('stockValueChart').getContext('2d');
new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: stockValueData.map(item => item.category),
        datasets: [{
            label: 'Stock Value ($)',
            data: stockValueData.map(item => item.value),
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