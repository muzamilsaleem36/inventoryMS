@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-receipt me-2"></i>
                        Expense Management
                    </h3>
                    @can('expense-create')
                        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Expense
                        </a>
                    @endcan
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-receipt fa-2x mb-2"></i>
                                    <h4>{{ $expenses->total() }}</h4>
                                    <p class="mb-0">Total Expenses</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                                    <h4>${{ number_format($expenses->sum('amount'), 2) }}</h4>
                                    <p class="mb-0">Total Amount</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-calendar fa-2x mb-2"></i>
                                    <h4>${{ number_format($expenses->whereMonth('expense_date', now()->month)->sum('amount'), 2) }}</h4>
                                    <p class="mb-0">This Month</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                    <h4>{{ $expenses->where('status', 'approved')->count() }}</h4>
                                    <p class="mb-0">Approved</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form method="GET" action="{{ route('expenses.index') }}" class="d-flex">
                                <input type="text" name="search" class="form-control me-2" placeholder="Search expenses..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex justify-content-end">
                                <form method="GET" action="{{ route('expenses.index') }}" class="d-flex">
                                    <select name="category" class="form-select me-2" onchange="this.form.submit()">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                                {{ ucfirst($category) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <select name="status" class="form-select me-2" onchange="this.form.submit()">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                    <select name="sort" class="form-select me-2" onchange="this.form.submit()">
                                        <option value="">Sort By</option>
                                        <option value="amount" {{ request('sort') == 'amount' ? 'selected' : '' }}>Amount</option>
                                        <option value="expense_date" {{ request('sort') == 'expense_date' ? 'selected' : '' }}>Date</option>
                                        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Expenses Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->id }}</td>
                                        <td>
                                            <div>
                                                <strong>{{ $expense->title }}</strong>
                                                @if($expense->reference_number)
                                                    <br><small class="text-muted">Ref: {{ $expense->reference_number }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucfirst($expense->category) }}</span>
                                        </td>
                                        <td>
                                            <strong>${{ number_format($expense->amount, 2) }}</strong>
                                            @if($expense->payment_method)
                                                <br><small class="text-muted">{{ ucfirst($expense->payment_method) }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                                        <td>
                                            @if($expense->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($expense->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($expense->user)
                                                <a href="{{ route('users.show', $expense->user->id) }}" class="text-decoration-none">
                                                    {{ $expense->user->name }}
                                                </a>
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('expense-view')
                                                    <a href="{{ route('expenses.show', $expense->id) }}" class="btn btn-sm btn-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('expense-edit')
                                                    <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('expense-delete')
                                                    <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this expense?')">
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
                                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No expenses found.</p>
                                            @can('expense-create')
                                                <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Add First Expense
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($expenses->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $expenses->links() }}
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
    document.querySelectorAll('select[name="category"], select[name="status"], select[name="sort"]').forEach(function(element) {
        element.addEventListener('change', function() {
            this.form.submit();
        });
    });
</script>
@endpush 