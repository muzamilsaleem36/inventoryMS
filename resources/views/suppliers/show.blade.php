@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-truck me-2"></i>
                        Supplier Details
                    </h3>
                    <div class="btn-group">
                        @can('supplier-edit')
                            <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endcan
                        @can('supplier-delete')
                            <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this supplier?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        @endcan
                        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-building me-2"></i>
                                        Company Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Company Name:</strong></td>
                                            <td>{{ $supplier->company_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Contact Person:</strong></td>
                                            <td>{{ $supplier->contact_person }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>
                                                @if($supplier->email)
                                                    <a href="mailto:{{ $supplier->email }}">{{ $supplier->email }}</a>
                                                @else
                                                    <span class="text-muted">Not provided</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>
                                                @if($supplier->phone)
                                                    <a href="tel:{{ $supplier->phone }}">{{ $supplier->phone }}</a>
                                                @else
                                                    <span class="text-muted">Not provided</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Address:</strong></td>
                                            <td>{{ $supplier->address ?? 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @if($supplier->status == 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-credit-card me-2"></i>
                                        Financial Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Tax Number:</strong></td>
                                            <td>{{ $supplier->tax_number ?? 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Credit Limit:</strong></td>
                                            <td>
                                                @if($supplier->credit_limit)
                                                    <span class="badge bg-info">${{ number_format($supplier->credit_limit, 2) }}</span>
                                                @else
                                                    <span class="text-muted">No limit set</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Current Balance:</strong></td>
                                            <td>
                                                @php
                                                    $balance = $supplier->purchases()->sum('total_amount') - $supplier->purchases()->sum('paid_amount');
                                                @endphp
                                                @if($balance > 0)
                                                    <span class="badge bg-warning">${{ number_format($balance, 2) }}</span>
                                                @else
                                                    <span class="badge bg-success">$0.00</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Payment Terms:</strong></td>
                                            <td>{{ $supplier->payment_terms ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created:</strong></td>
                                            <td>{{ $supplier->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Updated:</strong></td>
                                            <td>{{ $supplier->updated_at->format('M d, Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-chart-bar me-2"></i>
                                        Purchase Statistics
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="card bg-primary text-white">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                                    <h4>{{ $supplier->purchases()->count() }}</h4>
                                                    <p class="mb-0">Total Purchases</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-success text-white">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                                                    <h4>${{ number_format($supplier->purchases()->sum('total_amount'), 2) }}</h4>
                                                    <p class="mb-0">Total Amount</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-info text-white">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-calendar fa-2x mb-2"></i>
                                                    <h4>{{ $supplier->purchases()->whereMonth('created_at', now()->month)->count() }}</h4>
                                                    <p class="mb-0">This Month</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-warning text-white">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-box fa-2x mb-2"></i>
                                                    <h4>{{ $supplier->purchases()->with('items')->get()->sum(function($purchase) { return $purchase->items->sum('quantity'); }) }}</h4>
                                                    <p class="mb-0">Total Items</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-receipt me-2"></i>
                                        Recent Purchase Orders
                                    </h5>
                                    @can('purchase-create')
                                        <a href="{{ route('purchases.create') }}?supplier_id={{ $supplier->id }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> New Purchase
                                        </a>
                                    @endcan
                                </div>
                                <div class="card-body">
                                    @if($supplier->purchases()->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Purchase #</th>
                                                        <th>Date</th>
                                                        <th>Status</th>
                                                        <th>Total Amount</th>
                                                        <th>Paid Amount</th>
                                                        <th>Balance</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($supplier->purchases()->latest()->take(10)->get() as $purchase)
                                                        <tr>
                                                            <td>
                                                                <strong>#{{ $purchase->id }}</strong>
                                                            </td>
                                                            <td>{{ $purchase->created_at->format('M d, Y') }}</td>
                                                            <td>
                                                                @if($purchase->status == 'completed')
                                                                    <span class="badge bg-success">Completed</span>
                                                                @elseif($purchase->status == 'pending')
                                                                    <span class="badge bg-warning">Pending</span>
                                                                @else
                                                                    <span class="badge bg-danger">Cancelled</span>
                                                                @endif
                                                            </td>
                                                            <td>${{ number_format($purchase->total_amount, 2) }}</td>
                                                            <td>${{ number_format($purchase->paid_amount, 2) }}</td>
                                                            <td>
                                                                @php
                                                                    $balance = $purchase->total_amount - $purchase->paid_amount;
                                                                @endphp
                                                                @if($balance > 0)
                                                                    <span class="badge bg-warning">${{ number_format($balance, 2) }}</span>
                                                                @else
                                                                    <span class="badge bg-success">Paid</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @can('purchase-view')
                                                                    <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-sm btn-info">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                @endcan
                                                                @can('purchase-edit')
                                                                    <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn btn-sm btn-warning">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                @endcan
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @if($supplier->purchases()->count() > 10)
                                            <div class="text-center mt-3">
                                                <a href="{{ route('purchases.index') }}?supplier_id={{ $supplier->id }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-list"></i> View All Purchases
                                                </a>
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No purchase orders found for this supplier.</p>
                                            @can('purchase-create')
                                                <a href="{{ route('purchases.create') }}?supplier_id={{ $supplier->id }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Create First Purchase
                                                </a>
                                            @endcan
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 