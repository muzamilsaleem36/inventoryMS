@extends('layouts.app')

@section('title', $product->name . ' - Product Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-box"></i> {{ $product->name }}
                    @if($product->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </h1>
                <div class="page-options">
                    @can('manage_products')
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Product
                        </a>
                    @endcan
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Products
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Product Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Product Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if($product->image)
                                <img src="{{ asset('storage/products/' . $product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="img-fluid rounded mb-3" 
                                     style="max-height: 300px; width: 100%; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center rounded mb-3" 
                                     style="height: 300px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 150px;">Product Code:</th>
                                    <td>{{ $product->code }}</td>
                                </tr>
                                @if($product->barcode)
                                <tr>
                                    <th>Barcode:</th>
                                    <td>{{ $product->barcode }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Category:</th>
                                    <td>
                                        <span class="badge bg-primary">{{ $product->category->name }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Unit:</th>
                                    <td>{{ ucfirst($product->unit) }}</td>
                                </tr>
                                @if($product->store)
                                <tr>
                                    <th>Store:</th>
                                    <td>{{ $product->store->name }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Stock Tracking:</th>
                                    <td>
                                        @if($product->track_stock)
                                            <span class="badge bg-success">Enabled</span>
                                        @else
                                            <span class="badge bg-secondary">Disabled</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created:</th>
                                    <td>{{ $product->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $product->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($product->description)
                    <div class="mt-3">
                        <h6>Description</h6>
                        <p class="text-muted">{{ $product->description }}</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Recent Sales -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Sales</h5>
                </div>
                <div class="card-body">
                    @if($product->saleItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Sale #</th>
                                        <th>Customer</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->saleItems->take(10) as $saleItem)
                                    <tr>
                                        <td>
                                            <a href="{{ route('sales.show', $saleItem->sale) }}">
                                                {{ $saleItem->sale->sale_number }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($saleItem->sale->customer)
                                                {{ $saleItem->sale->customer->name }}
                                            @else
                                                <span class="text-muted">Walk-in Customer</span>
                                            @endif
                                        </td>
                                        <td>{{ $saleItem->quantity }}</td>
                                        <td>${{ number_format($saleItem->price, 2) }}</td>
                                        <td>${{ number_format($saleItem->subtotal, 2) }}</td>
                                        <td>{{ $saleItem->sale->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($product->saleItems->count() > 10)
                        <div class="text-center mt-3">
                            <a href="{{ route('reports.sales') }}?product_id={{ $product->id }}" class="btn btn-sm btn-primary">
                                View All Sales
                            </a>
                        </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                            <p>No sales recorded for this product yet.</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Recent Purchases -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Purchases</h5>
                </div>
                <div class="card-body">
                    @if($product->purchaseItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Purchase #</th>
                                        <th>Supplier</th>
                                        <th>Quantity</th>
                                        <th>Cost Price</th>
                                        <th>Total</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->purchaseItems->take(10) as $purchaseItem)
                                    <tr>
                                        <td>
                                            <a href="{{ route('purchases.show', $purchaseItem->purchase) }}">
                                                {{ $purchaseItem->purchase->purchase_number }}
                                            </a>
                                        </td>
                                        <td>{{ $purchaseItem->purchase->supplier->name }}</td>
                                        <td>{{ $purchaseItem->quantity }}</td>
                                        <td>${{ number_format($purchaseItem->cost_price, 2) }}</td>
                                        <td>${{ number_format($purchaseItem->subtotal, 2) }}</td>
                                        <td>{{ $purchaseItem->purchase->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($product->purchaseItems->count() > 10)
                        <div class="text-center mt-3">
                            <a href="{{ route('reports.purchases') }}?product_id={{ $product->id }}" class="btn btn-sm btn-primary">
                                View All Purchases
                            </a>
                        </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-truck fa-3x mb-3"></i>
                            <p>No purchases recorded for this product yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h3 mb-0">{{ $product->saleItems->sum('quantity') }}</div>
                            <div class="text-muted">Total Sold</div>
                        </div>
                        <div class="col-6">
                            <div class="h3 mb-0">${{ number_format($product->saleItems->sum('subtotal'), 2) }}</div>
                            <div class="text-muted">Total Revenue</div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h5 mb-0">{{ $product->saleItems->count() }}</div>
                            <div class="text-muted small">Sales Count</div>
                        </div>
                        <div class="col-6">
                            <div class="h5 mb-0">{{ $product->purchaseItems->count() }}</div>
                            <div class="text-muted small">Purchase Count</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pricing Information -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pricing Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 mb-0 text-danger">${{ number_format($product->purchase_price, 2) }}</div>
                                <div class="text-muted">Purchase Price</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 mb-0 text-success">${{ number_format($product->selling_price, 2) }}</div>
                                <div class="text-muted">Selling Price</div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <div class="h5 mb-0">
                            @if($product->getProfitMargin() > 0)
                                <span class="text-success">+{{ number_format($product->getProfitMargin(), 1) }}%</span>
                            @else
                                <span class="text-danger">{{ number_format($product->getProfitMargin(), 1) }}%</span>
                            @endif
                        </div>
                        <div class="text-muted">Profit Margin</div>
                    </div>
                    
                    <div class="mt-3">
                        <div class="d-flex justify-content-between">
                            <span>Profit per Unit:</span>
                            <span class="fw-bold">${{ number_format($product->selling_price - $product->purchase_price, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stock Information -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Stock Information</h5>
                </div>
                <div class="card-body">
                    @if($product->track_stock)
                        <div class="text-center mb-3">
                            <div class="h2 mb-0">{{ $product->stock_quantity }}</div>
                            <div class="text-muted">Current Stock</div>
                        </div>
                        
                        @if($product->isOutOfStock())
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Out of Stock!</strong>
                            </div>
                        @elseif($product->isLowStock())
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Low Stock Warning!</strong>
                            </div>
                        @else
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <strong>Stock Level OK</strong>
                            </div>
                        @endif
                        
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="text-muted small">Min Level</div>
                                <div class="h6">{{ $product->min_stock_level }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small">Max Level</div>
                                <div class="h6">{{ $product->max_stock_level }}</div>
                            </div>
                        </div>
                        
                        <!-- Stock Level Progress Bar -->
                        <div class="mt-3">
                            <div class="d-flex justify-content-between small text-muted">
                                <span>0</span>
                                <span>{{ $product->max_stock_level }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                @php
                                    $percentage = ($product->stock_quantity / $product->max_stock_level) * 100;
                                    $color = $product->isOutOfStock() ? 'danger' : ($product->isLowStock() ? 'warning' : 'success');
                                @endphp
                                <div class="progress-bar bg-{{ $color }}" 
                                     role="progressbar" 
                                     style="width: {{ min($percentage, 100) }}%">
                                </div>
                            </div>
                        </div>
                        
                        @can('manage_products')
                        <div class="mt-3 text-center">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#stockAdjustModal">
                                <i class="fas fa-plus-minus"></i> Adjust Stock
                            </button>
                        </div>
                        @endcan
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-times-circle fa-2x mb-3"></i>
                            <p>Stock tracking is disabled for this product.</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Barcode -->
            @if($product->barcode)
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Barcode</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <strong>{{ $product->barcode }}</strong>
                    </div>
                    <div class="mb-3">
                        <!-- Barcode would be generated here -->
                        <div class="bg-light p-3 rounded">
                            <i class="fas fa-barcode fa-3x"></i>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-print"></i> Print Barcode
                    </button>
                </div>
            </div>
            @endif
            
            <!-- Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @can('manage_products')
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Product
                            </a>
                        @endcan
                        
                        @if($product->barcode)
                            <a href="{{ route('barcodes.generate') }}?product_id={{ $product->id }}" class="btn btn-outline-secondary">
                                <i class="fas fa-barcode"></i> Generate Barcode
                            </a>
                        @endif
                        
                        <a href="{{ route('reports.products') }}?product_id={{ $product->id }}" class="btn btn-outline-info">
                            <i class="fas fa-chart-line"></i> View Reports
                        </a>
                        
                        @can('manage_products')
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash"></i> Delete Product
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Adjustment Modal -->
@can('manage_products')
<div class="modal fade" id="stockAdjustModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adjust Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('products.adjust-stock', $product) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Current Stock</label>
                        <input type="text" class="form-control" value="{{ $product->stock_quantity }}" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Adjustment Type</label>
                        <select class="form-select" name="type" required>
                            <option value="">Select Type</option>
                            <option value="increase">Increase Stock</option>
                            <option value="decrease">Decrease Stock</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="quantity" min="1" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea class="form-control" name="reason" rows="3" placeholder="Enter reason for adjustment"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Adjust Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<!-- Delete Confirmation Modal -->
@can('manage_products')
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this product?</p>
                <p class="text-danger"><strong>Warning:</strong> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('products.destroy', $product) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Product</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan
@endsection 