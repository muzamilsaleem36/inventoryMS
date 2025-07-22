@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-0">Create Purchase Order</h3>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Purchase Orders
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('purchases.store') }}" id="purchaseForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="supplier_id">Supplier <span class="text-danger">*</span></label>
                                    <select class="form-control @error('supplier_id') is-invalid @enderror" 
                                            id="supplier_id" name="supplier_id" required>
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expected_date">Expected Delivery Date</label>
                                    <input type="date" class="form-control @error('expected_date') is-invalid @enderror" 
                                           id="expected_date" name="expected_date" value="{{ old('expected_date') }}" 
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                    @error('expected_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="card-title mb-0">Items</h5>
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-sm btn-success" id="addItemBtn">
                                            <i class="fas fa-plus"></i> Add Item
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="itemsContainer">
                                    <!-- Items will be added here -->
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-8"></div>
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col">
                                                        <strong>Total:</strong>
                                                    </div>
                                                    <div class="col-auto">
                                                        <strong>$<span id="totalAmount">0.00</span></strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label for="notes">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" placeholder="Optional notes...">{{ old('notes') }}</textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Purchase Order
                            </button>
                            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let itemIndex = 0;
const products = @json($products);

$(document).ready(function() {
    // Add first item by default
    addItem();
    
    // Add item button
    $('#addItemBtn').on('click', function() {
        addItem();
    });
    
    // Remove item
    $(document).on('click', '.remove-item', function() {
        $(this).closest('.item-row').remove();
        updateTotal();
    });
    
    // Update total on quantity or price change
    $(document).on('input', '.quantity-input, .cost-input', function() {
        updateItemTotal($(this).closest('.item-row'));
        updateTotal();
    });
    
    // Product selection change
    $(document).on('change', '.product-select', function() {
        const productId = $(this).val();
        const row = $(this).closest('.item-row');
        
        if (productId) {
            const product = products.find(p => p.id == productId);
            if (product) {
                row.find('.cost-input').val(product.cost_price || 0);
                updateItemTotal(row);
                updateTotal();
            }
        }
    });
});

function addItem() {
    const html = `
        <div class="item-row border-bottom pb-3 mb-3">
            <div class="row">
                <div class="col-md-5">
                    <label class="form-label">Product</label>
                    <select class="form-control product-select" name="items[${itemIndex}][product_id]" required>
                        <option value="">Select Product</option>
                        ${products.map(product => `<option value="${product.id}">${product.name} (${product.sku})</option>`).join('')}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control quantity-input" name="items[${itemIndex}][quantity]" 
                           value="1" min="1" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Cost Price</label>
                    <input type="number" class="form-control cost-input" name="items[${itemIndex}][cost_price]" 
                           value="0" step="0.01" min="0" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Total</label>
                    <div class="form-control-plaintext">$<span class="item-total">0.00</span></div>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm remove-item d-block">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    $('#itemsContainer').append(html);
    itemIndex++;
}

function updateItemTotal(row) {
    const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
    const cost = parseFloat(row.find('.cost-input').val()) || 0;
    const total = quantity * cost;
    
    row.find('.item-total').text(total.toFixed(2));
}

function updateTotal() {
    let total = 0;
    $('.item-total').each(function() {
        total += parseFloat($(this).text()) || 0;
    });
    
    $('#totalAmount').text(total.toFixed(2));
}
</script>
@endsection 