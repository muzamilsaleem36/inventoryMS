@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>
                        User Details
                    </h3>
                    <div class="btn-group">
                        @can('user-edit')
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endcan
                        @can('user-delete')
                            @if($user->id != auth()->id() && $user->id != 1)
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            @endif
                        @endcan
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-user-circle me-2"></i>
                                        Profile Information
                                    </h5>
                                </div>
                                <div class="card-body text-center">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="img-fluid rounded-circle mb-3" style="max-height: 200px; max-width: 200px;">
                                    @else
                                        <div class="bg-secondary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 150px; height: 150px;">
                                            <i class="fas fa-user fa-4x text-white"></i>
                                        </div>
                                    @endif
                                    
                                    <h4 class="mb-2">{{ $user->name }}</h4>
                                    <p class="text-muted mb-3">{{ $user->email }}</p>
                                    
                                    @if($user->roles->count() > 0)
                                        <div class="mb-3">
                                            @foreach($user->roles as $role)
                                                <span class="badge bg-{{ $role->name == 'admin' ? 'danger' : ($role->name == 'manager' ? 'warning' : 'info') }} me-1">
                                                    {{ ucfirst($role->name) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    @if($user->status == 'active')
                                        <span class="badge bg-success fs-6">Active</span>
                                    @else
                                        <span class="badge bg-danger fs-6">Inactive</span>
                                    @endif
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Contact Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>
                                                <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                                @if($user->email_verified_at)
                                                    <i class="fas fa-check-circle text-success ms-1" title="Email verified"></i>
                                                @else
                                                    <i class="fas fa-exclamation-circle text-warning ms-1" title="Email not verified"></i>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>
                                                @if($user->phone)
                                                    <a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>
                                                @else
                                                    <span class="text-muted">Not provided</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Address:</strong></td>
                                            <td>
                                                @if($user->address)
                                                    {{ $user->address }}
                                                @else
                                                    <span class="text-muted">Not provided</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-clock me-2"></i>
                                        Account Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Member Since:</strong></td>
                                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Login:</strong></td>
                                            <td>
                                                @if($user->last_login)
                                                    {{ $user->last_login->format('M d, Y H:i') }}
                                                @else
                                                    <span class="text-muted">Never</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @if($user->status == 'active')
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

                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-chart-bar me-2"></i>
                                        Activity Statistics
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="card bg-primary text-white">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                                    <h4>{{ $user->sales()->count() ?? 0 }}</h4>
                                                    <p class="mb-0">Sales Made</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-success text-white">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                                                    <h4>${{ number_format($user->sales()->sum('total_amount') ?? 0, 2) }}</h4>
                                                    <p class="mb-0">Total Sales</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-info text-white">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-calendar fa-2x mb-2"></i>
                                                    <h4>{{ $user->sales()->whereMonth('created_at', now()->month)->count() ?? 0 }}</h4>
                                                    <p class="mb-0">This Month</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-warning text-white">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                                    <h4>{{ $user->sales()->whereDate('created_at', now())->count() ?? 0 }}</h4>
                                                    <p class="mb-0">Today</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-shield-alt me-2"></i>
                                        Roles & Permissions
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($user->roles->count() > 0)
                                        <div class="row">
                                            @foreach($user->roles as $role)
                                                <div class="col-md-6 mb-3">
                                                    <div class="card border-{{ $role->name == 'admin' ? 'danger' : ($role->name == 'manager' ? 'warning' : 'info') }}">
                                                        <div class="card-header bg-{{ $role->name == 'admin' ? 'danger' : ($role->name == 'manager' ? 'warning' : 'info') }} text-white">
                                                            <h6 class="mb-0">{{ ucfirst($role->name) }}</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            @if($role->permissions->count() > 0)
                                                                <div class="row">
                                                                    @foreach($role->permissions as $permission)
                                                                        <div class="col-12 mb-1">
                                                                            <small class="badge bg-light text-dark">{{ $permission->name }}</small>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <small class="text-muted">No specific permissions assigned</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-user-times fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No roles assigned to this user.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-history me-2"></i>
                                        Recent Sales
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($user->sales && $user->sales->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Sale #</th>
                                                        <th>Customer</th>
                                                        <th>Total Amount</th>
                                                        <th>Date</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($user->sales()->latest()->take(10)->get() as $sale)
                                                        <tr>
                                                            <td><strong>#{{ $sale->id }}</strong></td>
                                                            <td>
                                                                @if($sale->customer)
                                                                    <a href="{{ route('customers.show', $sale->customer->id) }}" class="text-decoration-none">
                                                                        {{ $sale->customer->name }}
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">Walk-in Customer</span>
                                                                @endif
                                                            </td>
                                                            <td>${{ number_format($sale->total_amount, 2) }}</td>
                                                            <td>{{ $sale->created_at->format('M d, Y') }}</td>
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
                                                                @can('sale-view')
                                                                    <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-sm btn-info">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                @endcan
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No sales recorded by this user.</p>
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