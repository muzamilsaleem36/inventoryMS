@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">Suppliers</h1>
                <div class="page-options">
                    @can('manage_suppliers')
                        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Supplier
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('suppliers.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search suppliers..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="is_active" class="form-select">
                        <option value="">All Status</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-refresh"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Suppliers Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                Suppliers ({{ $suppliers->total() }})
            </h5>
        </div>
        <div class="card-body">
            @if($suppliers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Contact</th>
                                <th>Tax Number</th>
                                <th>Credit Limit</th>
                                <th>Current Balance</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suppliers as $supplier)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $supplier->name }}</strong>
                                        @if($supplier->email)
                                            <br><small class="text-muted">{{ $supplier->email }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $supplier->company_name }}</td>
                                <td>{{ $supplier->phone }}</td>
                                <td>{{ $supplier->tax_number ?: 'N/A' }}</td>
                                <td>${{ number_format($supplier->credit_limit, 2) }}</td>
                                <td>
                                    @if($supplier->current_balance > 0)
                                        <span class="text-danger">${{ number_format($supplier->current_balance, 2) }}</span>
                                    @else
                                        <span class="text-success">${{ number_format($supplier->current_balance, 2) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($supplier->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('suppliers.show', $supplier) }}" 
                                           class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('manage_suppliers')
                                            <a href="{{ route('suppliers.edit', $supplier) }}" 
                                               class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" 
                                                  class="d-inline" onsubmit="return confirm('Are you sure you want to delete this supplier?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $suppliers->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No suppliers found</h4>
                    <p class="text-muted">Add your first supplier to get started</p>
                    @can('manage_suppliers')
                        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Supplier
                        </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 