@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-0">Receive Purchase Order - {{ $purchase->purchase_number }}</h3>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Purchase Order
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('purchases.process-receive', $purchase) }}" id="receiveForm">
                        @csrf
                        
                        <!-- Purchase Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Purchase Information</h6>
                                        <p class="mb-1"><strong>Supplier:</strong> {{ $purchase->supplier->name }}</p>
                                        <p class="mb-1"><strong>Order Date:</strong> {{ $purchase->created_at->format('M d, Y') }}</p>
                                        <p class="mb-0"><strong>Expected Date:</strong> 
                                            @if($purchase->expected_date)
                                                {{ \Carbon\Carbon::parse($purchase->expected_date)->format('M d, Y') }}
                                            @else
                                                Not set
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="received_date">Received Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('received_date') is-invalid @enderror" 
                                           id="received_date" name="received_date" 
                                           value="{{ old('received_date', date('Y-m-d')) }}" 
                                           max="{{ date('Y-m-d') }}" required>
                                    @error('received_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Items to Receive -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Items to Receive</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Product</th>
                                                <th class="text-center">Ordered</th>
                                                <th class="text-center">Received</th>
                                                <th class="text-right">Expected Cost</th>
                                                <th class="text-right">Actual Cost</th>
                                                <th class="text-right">Line Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($purchase->items as $item)
                                            <tr>
                                                <td>
                                                    @if($item->product->image)
                                                    <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                         alt="{{ $item->product->name }}" 
                                                         class="img-thumbnail me-2" style="width: 40px; height: 40px;">
                                                    @endif
                                                    <strong>{{ $item->product->name }}</strong>
                                                    <br><small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-primary">{{ $item->quantity }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <input type="number" 
                                                           class="form-control received-quantity" 
                                                           name="items[{{ $item->id }}][received_quantity]" 
                                                           value="{{ $item->quantity }}" 
                                                           min="0" 
                                                           max="{{ $item->quantity }}" 
                                                           data-item-id="{{ $item->id }}"
                                                           style="width: 80px;">
                                                </td>
                                                <td class="text-right">
                                                    ${{ number_format($item->cost_price, 2) }}
                                                </td>
                                                <td class="text-right">
                                                    <input type="number" 
                                                           class="form-control actual-cost" 
                                                           name="items[{{ $item->id }}][actual_cost]" 
                                                           value="{{ $item->cost_price }}" 
                                                           step="0.01" 
                                                           min="0"
                                                           data-item-id="{{ $item->id }}"
                                                           style="width: 100px;">
                                                </td>
                                                <td class="text-right">
                                                    <strong>$<span class="line-total" data-item-id="{{ $item->id }}">{{ number_format($item->total, 2) }}</span></strong>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="4">Total Received Value:</th>
                                                <th class="text-right">$<span id="totalReceived">0.00</span></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Receiving Notes -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="receiving_notes">Receiving Notes</label>
                                    <textarea class="form-control" id="receiving_notes" name="receiving_notes" rows="3" 
                                              placeholder="Any notes about the received items, damages, discrepancies, etc.">{{ old('receiving_notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="button" class="btn btn-warning" id="markPartialBtn">
                                            <i class="fas fa-check-circle"></i> Mark as Partially Received
                                        </button>
                                        <button type="button" class="btn btn-success" id="markCompleteBtn">
                                            <i class="fas fa-check-double"></i> Mark as Fully Received
                                        </button>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Process Receipt
                                        </button>
                                        <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Calculate line totals and update total
    function calculateTotals() {
        let totalReceived = 0;
        
        $('.received-quantity').each(function() {
            const itemId = $(this).data('item-id');
            const quantity = parseFloat($(this).val()) || 0;
            const cost = parseFloat($(`.actual-cost[data-item-id="${itemId}"]`).val()) || 0;
            const lineTotal = quantity * cost;
            
            $(`.line-total[data-item-id="${itemId}"]`).text(lineTotal.toFixed(2));
            totalReceived += lineTotal;
        });
        
        $('#totalReceived').text(totalReceived.toFixed(2));
    }
    
    // Update totals when quantities or costs change
    $('.received-quantity, .actual-cost').on('input', calculateTotals);
    
    // Initialize calculations
    calculateTotals();
    
    // Mark as partially received
    $('#markPartialBtn').on('click', function() {
        // You can add logic here to set some items to partial quantities
        calculateTotals();
    });
    
    // Mark as fully received
    $('#markCompleteBtn').on('click', function() {
        $('.received-quantity').each(function() {
            const maxQuantity = parseInt($(this).attr('max'));
            $(this).val(maxQuantity);
        });
        calculateTotals();
    });
    
    // Form validation
    $('#receiveForm').on('submit', function(e) {
        let hasReceivedItems = false;
        
        $('.received-quantity').each(function() {
            if (parseInt($(this).val()) > 0) {
                hasReceivedItems = true;
                return false;
            }
        });
        
        if (!hasReceivedItems) {
            e.preventDefault();
            alert('Please specify quantities received for at least one item.');
            return false;
        }
    });
});
</script>
@endsection 