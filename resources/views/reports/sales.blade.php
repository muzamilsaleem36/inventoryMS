@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Sales Reports</h3>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('reports.sales') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ $startDate }}" required>
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="{{ $endDate }}" required>
                            </div>
                            <div class="col-md-3">
                                <label for="customer_id" class="form-label">Customer</label>
                                <select class="form-select" id="customer_id" name="customer_id">
                                    <option value="">All Customers</option>
                                    @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method">
                                    <option value="">All Methods</option>
                                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                                    <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Apply Filters
                                </button>
                                <a href="{{ route('reports.sales') }}" class="btn btn-secondary">
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
                                            <h4>${{ number_format($totalSales, 2) }}</h4>
                                            <p class="mb-0">Total Sales</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-dollar-sign fa-2x"></i>
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
                                            <h4>{{ number_format($sales->count()) }}</h4>
                                            <p class="mb-0">Total Orders</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-shopping-cart fa-2x"></i>
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
                                            <h4>{{ number_format($totalItems) }}</h4>
                                            <p class="mb-0">Items Sold</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-boxes fa-2x"></i>
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
                                            <h4>${{ number_format($avgSale, 2) }}</h4>
                                            <p class="mb-0">Average Sale</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-chart-line fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Daily Sales Chart</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="dailySalesChart" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Payment Methods</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="paymentMethodChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Products -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Top Selling Products</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th class="text-center">Quantity Sold</th>
                                                    <th class="text-right">Total Revenue</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($topProducts as $product)
                                                <tr>
                                                    <td>{{ $product->name }}</td>
                                                    <td class="text-center">{{ $product->saleItems->sum('quantity') }}</td>
                                                    <td class="text-right">${{ number_format($product->saleItems->sum('total'), 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sales List -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Sales Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Sale #</th>
                                            <th>Date</th>
                                            <th>Customer</th>
                                            <th>Payment</th>
                                            <th class="text-right">Total</th>
                                            <th>Cashier</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sales as $sale)
                                        <tr>
                                            <td>
                                                <a href="{{ route('sales.show', $sale) }}">{{ $sale->sale_number }}</a>
                                            </td>
                                            <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                                            <td>
                                                @if($sale->customer)
                                                    {{ $sale->customer->name }}
                                                @else
                                                    Walk-in Customer
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $sale->payment_method == 'cash' ? 'success' : ($sale->payment_method == 'card' ? 'primary' : 'info') }}">
                                                    {{ ucfirst($sale->payment_method) }}
                                                </span>
                                            </td>
                                            <td class="text-right">${{ number_format($sale->total, 2) }}</td>
                                            <td>{{ $sale->user->name }}</td>
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
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Daily Sales Chart
const dailySalesData = @json($dailySales);
const ctx1 = document.getElementById('dailySalesChart').getContext('2d');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: dailySalesData.map(item => item.date),
        datasets: [{
            label: 'Sales ($)',
            data: dailySalesData.map(item => item.total),
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

// Payment Methods Chart
const paymentMethodsData = @json($paymentMethods);
const ctx2 = document.getElementById('paymentMethodChart').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: paymentMethodsData.map(item => item.method.charAt(0).toUpperCase() + item.method.slice(1)),
        datasets: [{
            data: paymentMethodsData.map(item => item.total),
            backgroundColor: [
                'rgba(75, 192, 192, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(231, 233, 237, 0.8)'
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
</script>
@endsection 