@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-store me-2"></i>
                        Store Details
                    </h3>
                    <div class="btn-group">
                        @can('store-edit')
                            <a href="{{ route('stores.edit', $store->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endcan
                        @can('store-delete')
                            <form action="{{ route('stores.destroy', $store->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this store?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        @endcan
                        <a href="{{ route('stores.index') }}" class="btn btn-secondary">
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
                                        <i class="fas fa-info-circle me-2"></i>
                                        Store Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($store->logo)
                                        <div class="text-center mb-3">
                                            <img src="{{ asset('storage/' . $store->logo) }}" alt="{{ $store->name }}" class="img-fluid rounded" style="max-height: 150px;">
                                        </div>
                                    @else
                                        <div class="text-center mb-3">
                                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="height: 120px;">
                                                <i class="fas fa-store fa-3x text-white"></i>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ $store->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Code:</strong></td>
                                            <td><code>{{ $store->code }}</code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @if($store->status == 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Manager:</strong></td>
                                            <td>
                                                @if($store->manager)
                                                    <a href="{{ route('users.show', $store->manager->id) }}" class="text-decoration-none">
                                                        {{ $store->manager->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">Not assigned</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created:</strong></td>
                                            <td>{{ $store->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Updated:</strong></td>
                                            <td>{{ $store->updated_at->format('M d, Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($store->description)
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-align-left me-2"></i>
                                            Description
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <p>{{ $store->description }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        Location
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Address:</strong></td>
                                            <td>{{ $store->address ?? 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>City:</strong></td>
                                            <td>{{ $store->city ?? 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Postal Code:</strong></td>
                                            <td>{{ $store->postal_code ?? 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Country:</strong></td>
                                            <td>{{ $store->country ?? 'Not provided' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-phone me-2"></i>
                                        Contact Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>
                                                @if($store->phone)
                                                    <a href="tel:{{ $store->phone }}">{{ $store->phone }}</a>
                                                @else
                                                    <span class="text-muted">Not provided</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>
                                                @if($store->email)
                                                    <a href="mailto:{{ $store->email }}">{{ $store->email }}</a>
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
                                        <i class="fas fa-cog me-2"></i>
                                        Business Settings
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Tax Number:</strong></td>
                                            <td>{{ $store->tax_number ?? 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Currency:</strong></td>
                                            <td>{{ $store->currency ?? 'Not set' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Timezone:</strong></td>
                                            <td>{{ $store->timezone ?? 'Not set' }}</td>
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
                                        Store Statistics
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="card bg-primary text-white">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-users fa-2x mb-2"></i>
                                                    <h4>{{ $store->users->count() }}</h4>
                                                    <p class="mb-0">Staff Members</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-success text-white">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                                    <h4>{{ $store->sales()->count() ?? 0 }}</h4>
                                                    <p class="mb-0">Total Sales</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-info text-white">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                                                    <h4>${{ number_format($store->sales()->sum('total_amount') ?? 0, 2) }}</h4>
                                                    <p class="mb-0">Revenue</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-warning text-white">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-calendar fa-2x mb-2"></i>
                                                    <h4>{{ $store->sales()->whereMonth('created_at', now()->month)->count() ?? 0 }}</h4>
                                                    <p class="mb-0">This Month</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-users me-2"></i>
                                        Staff Members
                                    </h5>
                                    @can('user-create')
                                        <a href="{{ route('users.create') }}?store_id={{ $store->id }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Add Staff
                                        </a>
                                    @endcan
                                </div>
                                <div class="card-body">
                                    @if($store->users->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Role</th>
                                                        <th>Status</th>
                                                        <th>Last Login</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($store->users as $user)
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    @if($user->avatar)
                                                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                                                    @else
                                                                        <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                                                            <i class="fas fa-user text-white"></i>
                                                                        </div>
                                                                    @endif
                                                                    {{ $user->name }}
                                                                </div>
                                                            </td>
                                                            <td>{{ $user->email }}</td>
                                                            <td>
                                                                @if($user->roles->count() > 0)
                                                                    @foreach($user->roles as $role)
                                                                        <span class="badge bg-{{ $role->name == 'admin' ? 'danger' : ($role->name == 'manager' ? 'warning' : 'info') }}">
                                                                            {{ ucfirst($role->name) }}
                                                                        </span>
                                                                    @endforeach
                                                                @else
                                                                    <span class="badge bg-secondary">No Role</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($user->status == 'active')
                                                                    <span class="badge bg-success">Active</span>
                                                                @else
                                                                    <span class="badge bg-danger">Inactive</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($user->last_login)
                                                                    {{ $user->last_login->diffForHumans() }}
                                                                @else
                                                                    <span class="text-muted">Never</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @can('user-view')
                                                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-info">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                @endcan
                                                                @can('user-edit')
                                                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning">
                                                                        <i class="fas fa-edit"></i>
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
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No staff members assigned to this store.</p>
                                            @can('user-create')
                                                <a href="{{ route('users.create') }}?store_id={{ $store->id }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Add First Staff Member
                                                </a>
                                            @endcan
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-chart-line me-2"></i>
                                        Recent Sales
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($store->sales && $store->sales->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Sale #</th>
                                                        <th>Customer</th>
                                                        <th>Staff</th>
                                                        <th>Amount</th>
                                                        <th>Date</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($store->sales()->latest()->take(10)->get() as $sale)
                                                        <tr>
                                                            <td><strong>#{{ $sale->id }}</strong></td>
                                                            <td>
                                                                @if($sale->customer)
                                                                    <a href="{{ route('customers.show', $sale->customer->id) }}" class="text-decoration-none">
                                                                        {{ $sale->customer->name }}
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">Walk-in</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($sale->user)
                                                                    <a href="{{ route('users.show', $sale->user->id) }}" class="text-decoration-none">
                                                                        {{ $sale->user->name }}
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">Unknown</span>
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
                                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No sales recorded for this store.</p>
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