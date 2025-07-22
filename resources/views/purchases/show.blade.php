@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-0">Purchase Order - {{ $purchase->purchase_number }}</h3>
                        </div>
                        <div class="col-auto">
                            <div class="btn-group" role="group">
                                @if($purchase->status == 'pending')
                                <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('purchases.receive', $purchase) }}" class="btn btn-success">
                                    <i class="fas fa-check"></i> Receive
                                </a>
                                @endif
                                <a href="{{ route('purchases.print', $purchase) }}" class="btn btn-primary" target="_blank">
                                    <i class="fas fa-print"></i> Print
                                </a>
                                <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Purchases
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Purchase Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Purchase Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>PO Number:</strong></td>
                                            <td>{{ $purchase->purchase_number }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date Created:</strong></td>
                                            <td>{{ $purchase->created_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created By:</strong></td>
                                            <td>{{ $purchase->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Supplier:</strong></td>
                                            <td>
                                                <a href="{{ route('suppliers.show', $purchase->supplier) }}">
                                                    {{ $purchase->supplier->name }}
                                                </a>
                                                @if($purchase->supplier->phone)
                                                <br><small class="text-muted">{{ $purchase->supplier->phone }}</small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Expected Date:</strong></td>
                                            <td>
                                                @if($purchase->expected_date)
                                                    {{ \Carbon\Carbon::parse($purchase->expected_date)->format('M d, Y') }}
                                                    @if($purchase->status == 'pending' && \Carbon\Carbon::parse($purchase->expected_date)->isPast())
                                                        <br><span class="badge badge-danger">Overdue</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @if($purchase->status == 'pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @elseif($purchase->status == 'partial')
                                                    <span class="badge badge-info">Partially Received</span>
                                                @elseif($purchase->status == 'completed')
                                                    <span class="badge badge-success">Completed</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ ucfirst($purchase->status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($purchase->received_date)
                                        <tr>
                                            <td><strong>Received Date:</strong></td>
                                            <td>{{ $purchase->received_date->format('M d, Y') }}</td>
                                        </tr>
                                        @endif
                                        @if($purchase->notes)
                                        <tr>
                                            <td><strong>Notes:</strong></td>
                                            <td>{{ $purchase->notes }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Purchase Summary -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Purchase Summary</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Order Total:</strong></td>
                                            <td class="text-right">${{ number_format($purchase->total, 2) }}</td>
                                        </tr>
                                        @if($purchase->received_total && $purchase->received_total != $purchase->total)
                                        <tr>
                                            <td><strong>Received Total:</strong></td>
                                            <td class="text-right">${{ number_format($purchase->received_total, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Remaining:</strong></td>
                                            <td class="text-right">${{ number_format($purchase->total - $purchase->received_total, 2) }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Total Items:</strong></td>
                                            <td class="text-right">{{ $purchase->items->sum('quantity') }}</td>
                                        </tr>
                                        @if($purchase->status != 'pending')
                                        <tr>
                                            <td><strong>Received Items:</strong></td>
                                            <td class="text-right">{{ $purchase->items->sum('received_quantity') }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Items -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Items Ordered</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>SKU</th>
                                                    <th class="text-center">Ordered</th>
                                                    @if($purchase->status != 'pending')
                                                    <th class="text-center">Received</th>
                                                    <th class="text-center">Remaining</th>
                                                    @endif
                                                    <th class="text-right">Cost Price</th>
                                                    @if($purchase->status != 'pending')
                                                    <th class="text-right">Actual Cost</th>
                                                    @endif
                                                    <th class="text-right">Total</th>
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
                                                        <a href="{{ route('products.show', $item->product) }}">
                                                            {{ $item->product->name }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $item->product->sku }}</td>
                                                    <td class="text-center">{{ $item->quantity }}</td>
                                                    @if($purchase->status != 'pending')
                                                    <td class="text-center">
                                                        {{ $item->received_quantity ?? 0 }}
                                                        @if($item->received_quantity > 0 && $item->received_quantity < $item->quantity)
                                                            <span class="badge badge-warning ml-1">Partial</span>
                                                        @elseif($item->received_quantity >= $item->quantity)
                                                            <span class="badge badge-success ml-1">Complete</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">{{ $item->quantity - ($item->received_quantity ?? 0) }}</td>
                                                    @endif
                                                    <td class="text-right">${{ number_format($item->cost_price, 2) }}</td>
                                                    @if($purchase->status != 'pending')
                                                    <td class="text-right">
                                                        @if($item->actual_cost)
                                                            ${{ number_format($item->actual_cost, 2) }}
                                                            @if($item->actual_cost != $item->cost_price)
                                                                <small class="text-muted d-block">
                                                                    ({{ $item->actual_cost > $item->cost_price ? '+' : '' }}${{ number_format($item->actual_cost - $item->cost_price, 2) }})
                                                                </small>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    @endif
                                                    <td class="text-right">${{ number_format($item->total, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-dark">
                                                    <th colspan="{{ $purchase->status == 'pending' ? '3' : '5' }}">Totals:</th>
                                                    <th class="text-center">{{ $purchase->items->sum('quantity') }}</th>
                                                    @if($purchase->status != 'pending')
                                                    <th class="text-center">{{ $purchase->items->sum('received_quantity') }}</th>
                                                    @endif
                                                    <th class="text-right">${{ number_format($purchase->items->sum('total'), 2) }}</th>
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