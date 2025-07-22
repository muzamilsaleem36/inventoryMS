@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-store me-2"></i>
                        Store Management
                    </h3>
                    @can('store-create')
                        <a href="{{ route('stores.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Store
                        </a>
                    @endcan
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-store fa-2x mb-2"></i>
                                    <h4>{{ $stores->total() }}</h4>
                                    <p class="mb-0">Total Stores</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                    <h4>{{ $stores->where('status', 'active')->count() }}</h4>
                                    <p class="mb-0">Active Stores</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                    <h4>{{ $stores->sum(function($store) { return $store->users->count(); }) }}</h4>
                                    <p class="mb-0">Total Staff</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-times-circle fa-2x mb-2"></i>
                                    <h4>{{ $stores->where('status', 'inactive')->count() }}</h4>
                                    <p class="mb-0">Inactive Stores</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('stores.index') }}" class="d-flex">
                                <input type="text" name="search" class="form-control me-2" placeholder="Search stores..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                <form method="GET" action="{{ route('stores.index') }}" class="d-flex">
                                    <select name="status" class="form-select me-2" onchange="this.form.submit()">
                                        <option value="">All Status</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <select name="sort" class="form-select me-2" onchange="this.form.submit()">
                                        <option value="">Sort By</option>
                                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Created Date</option>
                                        <option value="staff_count" {{ request('sort') == 'staff_count' ? 'selected' : '' }}>Staff Count</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Stores Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Store</th>
                                    <th>Address</th>
                                    <th>Contact</th>
                                    <th>Staff</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stores as $store)
                                    <tr>
                                        <td>{{ $store->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($store->logo)
                                                    <img src="{{ asset('storage/' . $store->logo) }}" alt="{{ $store->name }}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="bg-secondary rounded me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-store text-white"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong>{{ $store->name }}</strong>
                                                    @if($store->code)
                                                        <br><small class="text-muted">{{ $store->code }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($store->address)
                                                {{ Str::limit($store->address, 50) }}
                                                @if($store->city)
                                                    <br><small class="text-muted">{{ $store->city }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">No address</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($store->phone)
                                                <a href="tel:{{ $store->phone }}">{{ $store->phone }}</a>
                                                @if($store->email)
                                                    <br><a href="mailto:{{ $store->email }}">{{ $store->email }}</a>
                                                @endif
                                            @else
                                                <span class="text-muted">No contact</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $store->users->count() }}</span>
                                        </td>
                                        <td>
                                            @if($store->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $store->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('store-view')
                                                    <a href="{{ route('stores.show', $store->id) }}" class="btn btn-sm btn-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('store-edit')
                                                    <a href="{{ route('stores.edit', $store->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('store-delete')
                                                    <form action="{{ route('stores.destroy', $store->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this store?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-store fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No stores found.</p>
                                            @can('store-create')
                                                <a href="{{ route('stores.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Add First Store
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($stores->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $stores->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-submit form when filter changes
    document.querySelectorAll('select[name="status"], select[name="sort"]').forEach(function(element) {
        element.addEventListener('change', function() {
            this.form.submit();
        });
    });
</script>
@endpush 