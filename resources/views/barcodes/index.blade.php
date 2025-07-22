@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-barcode me-2"></i>
                        Barcode Management
                    </h3>
                    <div class="btn-group">
                        @can('barcode-create')
                            <a href="{{ route('barcodes.generate') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Generate Barcode
                            </a>
                        @endcan
                        @can('barcode-print')
                            <a href="{{ route('barcodes.bulk-print') }}" class="btn btn-success">
                                <i class="fas fa-print"></i> Bulk Print
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-barcode fa-2x mb-2"></i>
                                    <h4>{{ $barcodes->total() }}</h4>
                                    <p class="mb-0">Total Barcodes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-box fa-2x mb-2"></i>
                                    <h4>{{ $barcodes->whereNotNull('product_id')->count() }}</h4>
                                    <p class="mb-0">Products with Barcodes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-print fa-2x mb-2"></i>
                                    <h4>{{ $barcodes->where('print_count', '>', 0)->count() }}</h4>
                                    <p class="mb-0">Printed Barcodes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-calendar fa-2x mb-2"></i>
                                    <h4>{{ $barcodes->whereDate('created_at', today())->count() }}</h4>
                                    <p class="mb-0">Generated Today</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form method="GET" action="{{ route('barcodes.index') }}" class="d-flex">
                                <input type="text" name="search" class="form-control me-2" placeholder="Search barcodes..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex justify-content-end">
                                <form method="GET" action="{{ route('barcodes.index') }}" class="d-flex">
                                    <select name="type" class="form-select me-2" onchange="this.form.submit()">
                                        <option value="">All Types</option>
                                        <option value="CODE128" {{ request('type') == 'CODE128' ? 'selected' : '' }}>CODE128</option>
                                        <option value="CODE39" {{ request('type') == 'CODE39' ? 'selected' : '' }}>CODE39</option>
                                        <option value="EAN13" {{ request('type') == 'EAN13' ? 'selected' : '' }}>EAN13</option>
                                        <option value="UPC" {{ request('type') == 'UPC' ? 'selected' : '' }}>UPC</option>
                                    </select>
                                    <select name="status" class="form-select me-2" onchange="this.form.submit()">
                                        <option value="">All Status</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <select name="sort" class="form-select me-2" onchange="this.form.submit()">
                                        <option value="">Sort By</option>
                                        <option value="code" {{ request('sort') == 'code' ? 'selected' : '' }}>Code</option>
                                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Created Date</option>
                                        <option value="print_count" {{ request('sort') == 'print_count' ? 'selected' : '' }}>Print Count</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Barcodes Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>
                                        <input type="checkbox" id="select-all" class="form-check-input">
                                    </th>
                                    <th>Barcode</th>
                                    <th>Product</th>
                                    <th>Code</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Print Count</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($barcodes as $barcode)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="selected[]" value="{{ $barcode->id }}" class="form-check-input barcode-checkbox">
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <img src="{{ route('barcodes.image', $barcode->id) }}" alt="Barcode" style="height: 30px;">
                                            </div>
                                        </td>
                                        <td>
                                            @if($barcode->product)
                                                <div class="d-flex align-items-center">
                                                    @if($barcode->product->image)
                                                        <img src="{{ asset('storage/' . $barcode->product->image) }}" alt="{{ $barcode->product->name }}" class="rounded me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-secondary rounded me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                                            <i class="fas fa-box text-white"></i>
                                                        </div>
                                                    @endif
                                                    <a href="{{ route('products.show', $barcode->product->id) }}" class="text-decoration-none">
                                                        {{ $barcode->product->name }}
                                                    </a>
                                                </div>
                                            @else
                                                <span class="text-muted">No product assigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <code>{{ $barcode->code }}</code>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $barcode->type }}</span>
                                        </td>
                                        <td>
                                            @if($barcode->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $barcode->print_count ?? 0 }}</span>
                                        </td>
                                        <td>{{ $barcode->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('barcode-view')
                                                    <a href="{{ route('barcodes.show', $barcode->id) }}" class="btn btn-sm btn-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('barcode-print')
                                                    <a href="{{ route('barcodes.print', $barcode->id) }}" class="btn btn-sm btn-success" title="Print">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                @endcan
                                                @can('barcode-edit')
                                                    <a href="{{ route('barcodes.edit', $barcode->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('barcode-delete')
                                                    <form action="{{ route('barcodes.destroy', $barcode->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this barcode?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="fas fa-barcode fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No barcodes found.</p>
                                            @can('barcode-create')
                                                <a href="{{ route('barcodes.generate') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Generate First Barcode
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Bulk Actions -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <span class="me-2">Bulk Actions:</span>
                                <select id="bulk-action" class="form-select me-2" style="width: auto;">
                                    <option value="">Select action</option>
                                    <option value="print">Print Selected</option>
                                    <option value="activate">Activate Selected</option>
                                    <option value="deactivate">Deactivate Selected</option>
                                    <option value="delete">Delete Selected</option>
                                </select>
                                <button type="button" id="apply-bulk-action" class="btn btn-primary" disabled>
                                    <i class="fas fa-play"></i> Apply
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                <span id="selected-count">0 selected</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    @if($barcodes->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $barcodes->links() }}
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
    document.querySelectorAll('select[name="type"], select[name="status"], select[name="sort"]').forEach(function(element) {
        element.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Select all functionality
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.barcode-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
    });

    // Update selected count
    function updateSelectedCount() {
        const selectedCheckboxes = document.querySelectorAll('.barcode-checkbox:checked');
        const count = selectedCheckboxes.length;
        document.getElementById('selected-count').textContent = count + ' selected';
        document.getElementById('apply-bulk-action').disabled = count === 0;
    }

    // Individual checkbox change
    document.querySelectorAll('.barcode-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    // Apply bulk action
    document.getElementById('apply-bulk-action').addEventListener('click', function() {
        const action = document.getElementById('bulk-action').value;
        const selectedCheckboxes = document.querySelectorAll('.barcode-checkbox:checked');
        
        if (!action || selectedCheckboxes.length === 0) {
            alert('Please select an action and at least one barcode.');
            return;
        }

        if (action === 'delete' && !confirm('Are you sure you want to delete the selected barcodes?')) {
            return;
        }

        const selectedIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
        
        // Create and submit form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("barcodes.bulk-action") }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add action
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;
        form.appendChild(actionInput);
        
        // Add selected IDs
        selectedIds.forEach(id => {
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'selected[]';
            idInput.value = id;
            form.appendChild(idInput);
        });
        
        document.body.appendChild(form);
        form.submit();
    });
</script>
@endpush 