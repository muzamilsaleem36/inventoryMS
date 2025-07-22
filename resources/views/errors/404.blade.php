@extends('layouts.error')

@section('title', 'Page Not Found')

@section('content')
<div class="error-container">
    <div class="error-icon">
        <i class="fas fa-search text-primary" style="font-size: 4rem;"></i>
    </div>
    
    <div class="error-code">
        404
    </div>
    
    <div class="error-message">
        <h2>Page Not Found</h2>
        <p class="lead">{{ $message ?? 'The page you are looking for could not be found.' }}</p>
        <p class="text-muted">
            The page you requested might have been moved, deleted, or you might have entered the wrong URL.
        </p>
    </div>
    
    <div class="error-actions">
        <a href="{{ route('dashboard') }}" class="btn btn-primary">
            <i class="fas fa-home"></i> Back to Dashboard
        </a>
        
        @if(url()->previous() !== url()->current())
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Go Back
        </a>
        @endif
        
        <button type="button" class="btn btn-outline-secondary" onclick="window.location.reload()">
            <i class="fas fa-refresh"></i> Refresh Page
        </button>
    </div>
    
    <div class="error-help">
        <h5>What can you do?</h5>
        <ul class="list-unstyled">
            <li><i class="fas fa-check text-success"></i> Check the URL for typos</li>
            <li><i class="fas fa-check text-success"></i> Use the navigation menu to find what you need</li>
            <li><i class="fas fa-check text-success"></i> Go back to the dashboard and try again</li>
            <li><i class="fas fa-check text-success"></i> Contact your system administrator if the problem persists</li>
        </ul>
    </div>
    
    @auth
    <div class="error-shortcuts">
        <h5>Quick Links</h5>
        <div class="row">
            @can('manage_sales')
            <div class="col-md-3">
                <a href="{{ route('sales.index') }}" class="shortcut-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Sales</span>
                </a>
            </div>
            @endcan
            
            @can('manage_products')
            <div class="col-md-3">
                <a href="{{ route('products.index') }}" class="shortcut-link">
                    <i class="fas fa-boxes"></i>
                    <span>Products</span>
                </a>
            </div>
            @endcan
            
            @can('manage_customers')
            <div class="col-md-3">
                <a href="{{ route('customers.index') }}" class="shortcut-link">
                    <i class="fas fa-users"></i>
                    <span>Customers</span>
                </a>
            </div>
            @endcan
            
            @can('view_reports')
            <div class="col-md-3">
                <a href="{{ route('reports.sales') }}" class="shortcut-link">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
            </div>
            @endcan
        </div>
    </div>
    @endauth
</div>
@endsection 