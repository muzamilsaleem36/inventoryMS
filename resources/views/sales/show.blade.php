@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-0">Sale Details - {{ $sale->sale_number }}</h3>
                        </div>
                        <div class="col-auto">
                            <div class="btn-group" role="group">
                                <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-success">
                                    <i class="fas fa-receipt"></i> View Receipt
                                </a>
                                <a href="{{ route('sales.print', $sale) }}" class="btn btn-primary" target="_blank">
                                    <i class="fas fa-print"></i> Print
                                </a>
                                <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Sales
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Sale Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Sale Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Sale Number:</strong></td>
                                            <td>{{ $sale->sale_number }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date:</strong></td>
                                            <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Cashier:</strong></td>
                                            <td>{{ $sale->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Customer:</strong></td>
                                            <td>
                                                @if($sale->customer)
                                                    <a href="{{ route('customers.show', $sale->customer) }}">
                                                        {{ $sale->customer->name }}
                                                    </a>
                                                @else
                                                    Walk-in Customer
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Payment Method:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $sale->payment_method == 'cash' ? 'success' : ($sale->payment_method == 'card' ? 'primary' : 'info') }}">
                                                    {{ ucfirst($sale->payment_method) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $sale->status == 'completed' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($sale->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($sale->notes)
                                        <tr>
                                            <td><strong>Notes:</strong></td>
                                            <td>{{ $sale->notes }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Sale Summary -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Sale Summary</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Subtotal:</strong></td>
                                            <td class="text-right">${{ number_format($sale->subtotal, 2) }}</td>
                                        </tr>
                                        @if($sale->discount_amount > 0)
                                        <tr>
                                            <td><strong>Discount:</strong></td>
                                            <td class="text-right">-${{ number_format($sale->discount_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <small class="text-muted">
                                                    {{ $sale->discount_type == 'percentage' ? $sale->discount_value.'%' : '$'.number_format($sale->discount_value, 2) }} discount applied
                                                </small>
                                            </td>
                                        </tr>
                                        @endif
                                        @if($sale->tax_amount > 0)
                                        <tr>
                                            <td><strong>Tax ({{ $sale->tax_rate }}%):</strong></td>
                                            <td class="text-right">${{ number_format($sale->tax_amount, 2) }}</td>
                                        </tr>
                                        @endif
                                        <tr class="border-top">
                                            <td><strong>Total:</strong></td>
                                            <td class="text-right"><strong>${{ number_format($sale->total, 2) }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sale Items -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Items Sold</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>SKU</th>
                                                    <th class="text-center">Quantity</th>
                                                    <th class="text-right">Unit Price</th>
                                                    <th class="text-right">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($sale->items as $item)
                                                <tr>
                                                    <td>
                                                        @if($item->product->image)
                                                        <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                             alt="{{ $item->product->name }}" 
                                                             class="img-thumbnail me-2" style="width: 40px; height: 40px;">
                                                        @endif
                                                        <a href="{{ route('products.show', $item->product) }}">
                                                            {{ $item->product->name }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $item->product->sku }}</td>
                                                    <td class="text-center">{{ $item->quantity }}</td>
                                                    <td class="text-right">${{ number_format($item->price, 2) }}</td>
                                                    <td class="text-right">${{ number_format($item->total, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-dark">
                                                    <th colspan="2">Total Items:</th>
                                                    <th class="text-center">{{ $sale->items->sum('quantity') }}</th>
                                                    <th></th>
                                                    <th class="text-right">${{ number_format($sale->items->sum('total'), 2) }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
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