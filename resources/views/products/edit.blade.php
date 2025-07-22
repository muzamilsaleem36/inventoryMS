@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-edit"></i> Edit Product: {{ $product->name }}
                </h1>
                <div class="page-options">
                    <a href="{{ route('products.show', $product) }}" class="btn btn-info">
                        <i class="fas fa-eye"></i> View Product
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Products
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Product Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" id="productForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $product->name) }}" 
                                           placeholder="Enter product name" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label">Product Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" value="{{ old('code', $product->code) }}" 
                                           placeholder="Enter product code" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="barcode" class="form-label">Barcode</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('barcode') is-invalid @enderror" 
                                               id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}" 
                                               placeholder="Enter barcode or leave empty to auto-generate">
                                        <button type="button" class="btn btn-outline-secondary" id="generateBarcode">
                                            <i class="fas fa-barcode"></i> Generate
                                        </button>
                                    </div>
                                    @error('barcode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" 
                                            id="category_id" name="category_id" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Enter product description">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="purchase_price" class="form-label">Purchase Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control @error('purchase_price') is-invalid @enderror" 
                                               id="purchase_price" name="purchase_price" 
                                               value="{{ old('purchase_price', $product->purchase_price) }}" 
                                               step="0.01" min="0" placeholder="0.00" required>
                                    </div>
                                    @error('purchase_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="selling_price" class="form-label">Selling Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control @error('selling_price') is-invalid @enderror" 
                                               id="selling_price" name="selling_price" 
                                               value="{{ old('selling_price', $product->selling_price) }}" 
                                               step="0.01" min="0" placeholder="0.00" required>
                                    </div>
                                    @error('selling_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="stock_quantity" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror" 
                                           id="stock_quantity" name="stock_quantity" 
                                           value="{{ old('stock_quantity', $product->stock_quantity) }}" 
                                           min="0" placeholder="0" required>
                                    @error('stock_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="min_stock_level" class="form-label">Min Stock Level <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('min_stock_level') is-invalid @enderror" 
                                           id="min_stock_level" name="min_stock_level" 
                                           value="{{ old('min_stock_level', $product->min_stock_level) }}" 
                                           min="0" placeholder="5" required>
                                    @error('min_stock_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="max_stock_level" class="form-label">Max Stock Level <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('max_stock_level') is-invalid @enderror" 
                                           id="max_stock_level" name="max_stock_level" 
                                           value="{{ old('max_stock_level', $product->max_stock_level) }}" 
                                           min="1" placeholder="1000" required>
                                    @error('max_stock_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="unit" class="form-label">Unit <span class="text-danger">*</span></label>
                                    <select class="form-select @error('unit') is-invalid @enderror" id="unit" name="unit" required>
                                        <option value="">Select Unit</option>
                                        <option value="pcs" {{ old('unit', $product->unit) == 'pcs' ? 'selected' : '' }}>Pieces</option>
                                        <option value="kg" {{ old('unit', $product->unit) == 'kg' ? 'selected' : '' }}>Kilogram</option>
                                        <option value="g" {{ old('unit', $product->unit) == 'g' ? 'selected' : '' }}>Gram</option>
                                        <option value="l" {{ old('unit', $product->unit) == 'l' ? 'selected' : '' }}>Liter</option>
                                        <option value="ml" {{ old('unit', $product->unit) == 'ml' ? 'selected' : '' }}>Milliliter</option>
                                        <option value="box" {{ old('unit', $product->unit) == 'box' ? 'selected' : '' }}>Box</option>
                                        <option value="pack" {{ old('unit', $product->unit) == 'pack' ? 'selected' : '' }}>Pack</option>
                                        <option value="bottle" {{ old('unit', $product->unit) == 'bottle' ? 'selected' : '' }}>Bottle</option>
                                        <option value="can" {{ old('unit', $product->unit) == 'can' ? 'selected' : '' }}>Can</option>
                                        <option value="dozen" {{ old('unit', $product->unit) == 'dozen' ? 'selected' : '' }}>Dozen</option>
                                    </select>
                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            @if(auth()->user()->hasRole('admin'))
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="store_id" class="form-label">Store</label>
                                    <select class="form-select @error('store_id') is-invalid @enderror" id="store_id" name="store_id">
                                        <option value="">Select Store</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}" 
                                                    {{ old('store_id', $product->store_id) == $store->id ? 'selected' : '' }}>
                                                {{ $store->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('store_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Product Image</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Upload product image (JPEG, PNG, JPG, GIF, max 2MB)</div>
                        </div>

                        @if($product->image)
                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('storage/products/' . $product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="img-thumbnail me-3" 
                                     style="max-width: 150px;">
                                <div>
                                    <button type="button" class="btn btn-sm btn-danger" id="removeImage">
                                        <i class="fas fa-trash"></i> Remove Current Image
                                    </button>
                                    <input type="hidden" name="remove_image" id="removeImageInput" value="0">
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="mb-3">
                            <div id="imagePreview" class="mt-2" style="display: none;">
                                <img id="previewImg" src="#" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="track_stock" name="track_stock" value="1" 
                                           {{ old('track_stock', $product->track_stock) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="track_stock">
                                        Track Stock
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Product Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 mb-0">{{ $product->stock_quantity }}</div>
                                <div class="text-muted">Current Stock</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 mb-0">{{ number_format($product->getProfitMargin(), 1) }}%</div>
                                <div class="text-muted">Profit Margin</div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h6 mb-0">{{ $product->saleItems()->count() }}</div>
                                <div class="text-muted small">Sales</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h6 mb-0">{{ $product->purchaseItems()->count() }}</div>
                                <div class="text-muted small">Purchases</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Price Calculator</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Profit Margin</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="profitMargin" 
                                   placeholder="0" step="0.01" 
                                   value="{{ number_format($product->getProfitMargin(), 2) }}">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Suggested Selling Price</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="text" class="form-control" id="suggestedPrice" readonly>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-sm btn-primary" id="applySuggestedPrice">
                        Apply Suggested Price
                    </button>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Stock Status</h5>
                </div>
                <div class="card-body">
                    @if($product->isOutOfStock())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Out of Stock</strong>
                        </div>
                    @elseif($product->isLowStock())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Low Stock</strong>
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <strong>In Stock</strong>
                        </div>
                    @endif
                    
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="text-muted small">Min Level</div>
                            <div class="h6">{{ $product->min_stock_level }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small">Current</div>
                            <div class="h6">{{ $product->stock_quantity }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small">Max Level</div>
                            <div class="h6">{{ $product->max_stock_level }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
        }
    });
    
    // Remove current image
    const removeImageBtn = document.getElementById('removeImage');
    if (removeImageBtn) {
        removeImageBtn.addEventListener('click', function() {
            const confirmation = confirm('Are you sure you want to remove the current image?');
            if (confirmation) {
                document.getElementById('removeImageInput').value = '1';
                this.closest('.mb-3').style.display = 'none';
            }
        });
    }
    
    // Generate barcode
    document.getElementById('generateBarcode').addEventListener('click', function() {
        const barcode = '2' + Math.floor(Math.random() * 1000000000000).toString().padStart(12, '0');
        document.getElementById('barcode').value = barcode;
    });
    
    // Price calculator
    const purchasePriceInput = document.getElementById('purchase_price');
    const sellingPriceInput = document.getElementById('selling_price');
    const profitMarginInput = document.getElementById('profitMargin');
    const suggestedPriceInput = document.getElementById('suggestedPrice');
    const applySuggestedPriceBtn = document.getElementById('applySuggestedPrice');
    
    function calculateSuggestedPrice() {
        const purchasePrice = parseFloat(purchasePriceInput.value) || 0;
        const profitMargin = parseFloat(profitMarginInput.value) || 0;
        
        if (purchasePrice > 0 && profitMargin > 0) {
            const suggestedPrice = purchasePrice * (1 + profitMargin / 100);
            suggestedPriceInput.value = suggestedPrice.toFixed(2);
        } else {
            suggestedPriceInput.value = '';
        }
    }
    
    function calculateProfitMargin() {
        const purchasePrice = parseFloat(purchasePriceInput.value) || 0;
        const sellingPrice = parseFloat(sellingPriceInput.value) || 0;
        
        if (purchasePrice > 0 && sellingPrice > 0) {
            const margin = ((sellingPrice - purchasePrice) / purchasePrice) * 100;
            profitMarginInput.value = margin.toFixed(2);
        } else {
            profitMarginInput.value = '';
        }
    }
    
    purchasePriceInput.addEventListener('input', calculateSuggestedPrice);
    profitMarginInput.addEventListener('input', calculateSuggestedPrice);
    sellingPriceInput.addEventListener('input', calculateProfitMargin);
    
    // Initialize suggested price
    calculateSuggestedPrice();
    
    applySuggestedPriceBtn.addEventListener('click', function() {
        const suggestedPrice = suggestedPriceInput.value;
        if (suggestedPrice) {
            sellingPriceInput.value = suggestedPrice;
            calculateProfitMargin();
        }
    });
    
    // Form validation
    const form = document.getElementById('productForm');
    form.addEventListener('submit', function(e) {
        const minStock = parseInt(document.getElementById('min_stock_level').value);
        const maxStock = parseInt(document.getElementById('max_stock_level').value);
        
        if (maxStock <= minStock) {
            e.preventDefault();
            alert('Maximum stock level must be greater than minimum stock level.');
            return false;
        }
        
        const purchasePrice = parseFloat(purchasePriceInput.value);
        const sellingPrice = parseFloat(sellingPriceInput.value);
        
        if (sellingPrice < purchasePrice) {
            if (!confirm('Selling price is less than purchase price. This will result in a loss. Are you sure you want to continue?')) {
                e.preventDefault();
                return false;
            }
        }
    });
});
</script>
@endpush
@endsection 