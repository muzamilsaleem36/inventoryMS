@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">Customer Details</h1>
                <div class="page-options">
                    <div class="btn-group">
                        @can('manage_customers')
                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Customer
                            </a>
                        @endcan
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Customers
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Customer Information -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Name:</strong></div>
                        <div class="col-sm-8">{{ $customer->name }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Email:</strong></div>
                        <div class="col-sm-8">{{ $customer->email ?: 'N/A' }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Phone:</strong></div>
                        <div class="col-sm-8">{{ $customer->phone }}</div>
                    </div>
                    
                    @if($customer->date_of_birth)
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Date of Birth:</strong></div>
                        <div class="col-sm-8">{{ $customer->date_of_birth->format('M j, Y') }}</div>
                    </div>
                    @endif
                    
                    @if($customer->gender)
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Gender:</strong></div>
                        <div class="col-sm-8">{{ ucfirst($customer->gender) }}</div>
                    </div>
                    @endif
                    
                    @if($customer->address)
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Address:</strong></div>
                        <div class="col-sm-8">{{ $customer->address }}</div>
                    </div>
                    @endif
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Credit Limit:</strong></div>
                        <div class="col-sm-8">${{ number_format($customer->credit_limit, 2) }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Current Balance:</strong></div>
                        <div class="col-sm-8">
                            @if($customer->current_balance > 0)
                                <span class="text-danger">${{ number_format($customer->current_balance, 2) }}</span>
                            @else
                                <span class="text-success">${{ number_format($customer->current_balance, 2) }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Status:</strong></div>
                        <div class="col-sm-8">
                            @if($customer->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Joined:</strong></div>
                        <div class="col-sm-8">{{ $customer->created_at->format('M j, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase Statistics -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Purchase Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-primary">{{ $customer->getTotalOrders() }}</h4>
                                <p class="text-muted mb-0">Total Orders</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-success">${{ number_format($customer->getTotalPurchases(), 2) }}</h4>
                                <p class="text-muted mb-0">Total Purchases</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-info">
                                    ${{ number_format($customer->getTotalOrders() > 0 ? $customer->getTotalPurchases() / $customer->getTotalOrders() : 0, 2) }}
                                </h4>
                                <p class="text-muted mb-0">Average Order</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-warning">
                                    {{ $customer->sales()->where('created_at', '>=', now()->subDays(30))->count() }}
                                </h4>
                                <p class="text-muted mb-0">Orders (30 days)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Orders</h5>
                </div>
                <div class="card-body">
                    @if($customer->sales->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->sales as $sale)
                                    <tr>
                                        <td>{{ $sale->sale_number }}</td>
                                        <td>{{ $sale->created_at->format('M j, Y') }}</td>
                                        <td>${{ number_format($sale->total, 2) }}</td>
                                        <td>
                                            @if($sale->status == 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($sale->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-danger">Cancelled</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No orders yet</h5>
                            <p class="text-muted">This customer hasn't made any purchases yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 