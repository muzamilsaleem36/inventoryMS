@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-0">Create New Sale</h3>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Sales
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        For a better sales experience, use our <a href="{{ route('sales.pos') }}" class="btn btn-sm btn-primary ms-2">POS System</a>
                    </div>
                    
                    <form method="POST" action="{{ route('sales.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="customer_id">Customer</label>
                                    <select class="form-select @error('customer_id') is-invalid @enderror" 
                                            id="customer_id" name="customer_id">
                                        <option value="">Walk-in Customer</option>
                                        @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} - {{ $customer->phone }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                                    <select class="form-select @error('payment_method') is-invalid @enderror" 
                                            id="payment_method" name="payment_method" required>
                                        <option value="">Select Payment Method</option>
                                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                                        <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                    </select>
                                    @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Items Section -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="card-title mb-0">Sale Items</h5>
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-sm btn-success" id="addItem">
                                            <i class="fas fa-plus"></i> Add Item
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="itemsContainer">
                                    <!-- Items will be added here dynamically -->
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-8"></div>
                                    <div class="col-md-4">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td>Subtotal:</td>
                                                <td class="text-end">$<span id="subtotal">0.00</span></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <select class="form-select" id="discountType" name="discount_type">
                                                            <option value="percentage">% Discount</option>
                                                            <option value="fixed">$ Discount</option>
                                                        </select>
                                                        <input type="number" class="form-control" id="discountValue" 
                                                               name="discount_value" placeholder="0" step="0.01" min="0">
                                                    </div>
                                                </td>
                                                <td class="text-end">-$<span id="discountAmount">0.00</span></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" class="form-control" id="taxRate" 
                                                               name="tax_rate" placeholder="Tax %" step="0.01" min="0" max="100" 
                                                               value="{{ $settings['tax_rate'] ?? 0 }}">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </td>
                                                <td class="text-end">+$<span id="taxAmount">0.00</span></td>
                                            </tr>
                                            <tr class="border-top">
                                                <td><strong>Total:</strong></td>
                                                <td class="text-end"><strong>$<span id="total">0.00</span></strong></td>
                                            </tr>
                                        </table>
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

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Sale
                            </button>
                            <a href="{{ route('sales.index') }}" class="btn btn-secondary">
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
    
    $('#addItem').on('click', function() {
        addItem();
    });
    
    $(document).on('click', '.remove-item', function() {
        $(this).closest('.item-row').remove();
        calculateTotals();
    });
    
    $(document).on('change', '.product-select', function() {
        const productId = $(this).val();
        const row = $(this).closest('.item-row');
        
        if (productId) {
            const product = products.find(p => p.id == productId);
            if (product) {
                row.find('.price-input').val(product.selling_price);
                row.find('.max-quantity').text(product.stock);
                row.find('.quantity-input').attr('max', product.stock);
                calculateItemTotal(row);
            }
        }
    });
    
    $(document).on('input', '.quantity-input, .price-input', function() {
        calculateItemTotal($(this).closest('.item-row'));
    });
    
    $('#discountType, #discountValue, #taxRate').on('input', calculateTotals);
});

function addItem() {
    const html = `
        <div class="item-row border-bottom pb-3 mb-3">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Product</label>
                    <select class="form-control product-select" name="items[${itemIndex}][product_id]" required>
                        <option value="">Select Product</option>
                        ${products.map(product => `<option value="${product.id}">${product.name} - $${product.selling_price}</option>`).join('')}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control quantity-input" name="items[${itemIndex}][quantity]" 
                           value="1" min="1" required>
                    <small class="text-muted">Max: <span class="max-quantity">-</span></small>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Price</label>
                    <input type="number" class="form-control price-input" name="items[${itemIndex}][price]" 
                           value="0" step="0.01" min="0" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Total</label>
                    <div class="form-control-plaintext"><strong>$<span class="item-total">0.00</span></strong></div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-item">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    `;
    
    $('#itemsContainer').append(html);
    itemIndex++;
}

function calculateItemTotal(row) {
    const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
    const price = parseFloat(row.find('.price-input').val()) || 0;
    const total = quantity * price;
    
    row.find('.item-total').text(total.toFixed(2));
    calculateTotals();
}

function calculateTotals() {
    let subtotal = 0;
    
    $('.item-total').each(function() {
        subtotal += parseFloat($(this).text()) || 0;
    });
    
    $('#subtotal').text(subtotal.toFixed(2));
    
    // Calculate discount
    const discountType = $('#discountType').val();
    const discountValue = parseFloat($('#discountValue').val()) || 0;
    let discountAmount = 0;
    
    if (discountValue > 0) {
        if (discountType === 'percentage') {
            discountAmount = (subtotal * discountValue) / 100;
        } else {
            discountAmount = discountValue;
        }
    }
    
    $('#discountAmount').text(discountAmount.toFixed(2));
    
    const discountedTotal = subtotal - discountAmount;
    
    // Calculate tax
    const taxRate = parseFloat($('#taxRate').val()) || 0;
    const taxAmount = (discountedTotal * taxRate) / 100;
    
    $('#taxAmount').text(taxAmount.toFixed(2));
    
    const total = discountedTotal + taxAmount;
    $('#total').text(total.toFixed(2));
}
</script>
@endsection 