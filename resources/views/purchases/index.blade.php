@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-0">Purchase Orders</h3>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> New Purchase Order
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($purchases->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>PO #</th>
                                    <th>Supplier</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Expected Date</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchases as $purchase)
                                <tr>
                                    <td>
                                        <strong>{{ $purchase->purchase_number }}</strong>
                                    </td>
                                    <td>
                                        {{ $purchase->supplier->name }}
                                    </td>
                                    <td>
                                        <strong>${{ number_format($purchase->total, 2) }}</strong>
                                        @if($purchase->received_total && $purchase->received_total != $purchase->total)
                                        <br><small class="text-muted">Received: ${{ number_format($purchase->received_total, 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($purchase->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($purchase->status == 'partial')
                                            <span class="badge badge-info">Partial</span>
                                        @elseif($purchase->status == 'completed')
                                            <span class="badge badge-success">Completed</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($purchase->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($purchase->expected_date)
                                            {{ \Carbon\Carbon::parse($purchase->expected_date)->format('M d, Y') }}
                                            @if($purchase->status == 'pending' && \Carbon\Carbon::parse($purchase->expected_date)->isPast())
                                                <br><small class="text-danger">Overdue</small>
                                            @endif
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $purchase->created_at->format('M d, Y') }}
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($purchase->status == 'pending')
                                            <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('purchases.receive', $purchase) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            @endif
                                            <a href="{{ route('purchases.print', $purchase) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            @if($purchase->status != 'completed')
                                            <form method="POST" action="{{ route('purchases.destroy', $purchase) }}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this purchase order?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $purchases->links() }}
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-basket fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No purchase orders found</h4>
                        <p class="text-muted">Create your first purchase order to restock inventory</p>
                        <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Purchase Order
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 