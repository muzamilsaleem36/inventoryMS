@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-0">Sales Management</h3>
                        </div>
                        <div class="col-auto">
                            <div class="btn-group" role="group">
                                <a href="{{ route('sales.pos') }}" class="btn btn-primary">
                                    <i class="fas fa-cash-register"></i> POS System
                                </a>
                                <a href="{{ route('sales.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i> New Sale
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($sales->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Sale #</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $sale)
                                <tr>
                                    <td>
                                        <strong>{{ $sale->sale_number }}</strong>
                                    </td>
                                    <td>
                                        @if($sale->customer)
                                            {{ $sale->customer->name }}
                                        @else
                                            <span class="text-muted">Walk-in Customer</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>${{ number_format($sale->total, 2) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $sale->payment_method == 'cash' ? 'success' : ($sale->payment_method == 'card' ? 'primary' : 'info') }}">
                                            {{ ucfirst($sale->payment_method) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $sale->status == 'completed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($sale->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $sale->created_at->format('M d, Y h:i A') }}
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                            <a href="{{ route('sales.print', $sale) }}" class="btn btn-sm btn-outline-info" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            @if(Auth::user()->hasRole('admin'))
                                            <form method="POST" action="{{ route('sales.destroy', $sale) }}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this sale?')">
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
                    
                    {{ $sales->links() }}
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No sales found</h4>
                        <p class="text-muted">Start by creating your first sale</p>
                        <a href="{{ route('sales.pos') }}" class="btn btn-primary">
                            <i class="fas fa-cash-register"></i> Open POS System
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 