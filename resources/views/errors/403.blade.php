@extends('layouts.error')

@section('title', 'Access Denied')

@section('content')
<div class="error-container">
    <div class="error-icon">
        <i class="fas fa-lock text-danger" style="font-size: 4rem;"></i>
    </div>
    
    <div class="error-code">
        403
    </div>
    
    <div class="error-message">
        <h2>Access Denied</h2>
        <p class="lead">{{ $message ?? 'You do not have permission to access this resource.' }}</p>
        <p class="text-muted">
            This page or action requires specific permissions that your account does not have.
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
        
        @auth
        @if(auth()->user()->hasRole('admin'))
        <a href="{{ route('users.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-user-cog"></i> Manage Users
        </a>
        @endif
        @endauth
    </div>
    
    <div class="error-help">
        <h5>Why am I seeing this?</h5>
        <ul class="list-unstyled">
            <li><i class="fas fa-info-circle text-info"></i> You don't have the required permissions for this action</li>
            <li><i class="fas fa-info-circle text-info"></i> Your user role doesn't allow access to this resource</li>
            <li><i class="fas fa-info-circle text-info"></i> The resource may be restricted to certain user groups</li>
        </ul>
    </div>
    
    @auth
    <div class="current-permissions">
        <h5>Your Current Access Level</h5>
        <div class="permission-info">
            <p><strong>User:</strong> {{ auth()->user()->name }}</p>
            <p><strong>Role:</strong> 
                @foreach(auth()->user()->getRoleNames() as $role)
                    <span class="badge bg-primary">{{ ucfirst($role) }}</span>
                @endforeach
            </p>
            @if(auth()->user()->store)
            <p><strong>Store:</strong> {{ auth()->user()->store->name }}</p>
            @endif
        </div>
    </div>
    
    <div class="error-help">
        <h5>What can you do?</h5>
        <ul class="list-unstyled">
            <li><i class="fas fa-check text-success"></i> Contact your system administrator to request access</li>
            <li><i class="fas fa-check text-success"></i> Return to the dashboard and use available features</li>
            <li><i class="fas fa-check text-success"></i> Check if you're logged in with the correct account</li>
            <li><i class="fas fa-check text-success"></i> Ask your manager about upgrading your permissions</li>
        </ul>
    </div>
    
    <div class="error-shortcuts">
        <h5>Available Actions</h5>
        <div class="row">
            @can('manage_sales')
            <div class="col-md-4">
                <a href="{{ route('sales.index') }}" class="shortcut-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Sales</span>
                </a>
            </div>
            @endcan
            
            @can('manage_products')
            <div class="col-md-4">
                <a href="{{ route('products.index') }}" class="shortcut-link">
                    <i class="fas fa-boxes"></i>
                    <span>Products</span>
                </a>
            </div>
            @else
            <div class="col-md-4">
                <div class="shortcut-link disabled" style="opacity: 0.5; cursor: not-allowed;">
                    <i class="fas fa-boxes"></i>
                    <span>Products</span>
                    <small class="text-muted d-block">No Access</small>
                </div>
            </div>
            @endcan
            
            @can('manage_customers')
            <div class="col-md-4">
                <a href="{{ route('customers.index') }}" class="shortcut-link">
                    <i class="fas fa-users"></i>
                    <span>Customers</span>
                </a>
            </div>
            @else
            <div class="col-md-4">
                <div class="shortcut-link disabled" style="opacity: 0.5; cursor: not-allowed;">
                    <i class="fas fa-users"></i>
                    <span>Customers</span>
                    <small class="text-muted d-block">No Access</small>
                </div>
            </div>
            @endcan
            
            @can('view_reports')
            <div class="col-md-4">
                <a href="{{ route('reports.sales') }}" class="shortcut-link">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
            </div>
            @else
            <div class="col-md-4">
                <div class="shortcut-link disabled" style="opacity: 0.5; cursor: not-allowed;">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                    <small class="text-muted d-block">No Access</small>
                </div>
            </div>
            @endcan
            
            @can('manage_users')
            <div class="col-md-4">
                <a href="{{ route('users.index') }}" class="shortcut-link">
                    <i class="fas fa-user-cog"></i>
                    <span>User Management</span>
                </a>
            </div>
            @else
            <div class="col-md-4">
                <div class="shortcut-link disabled" style="opacity: 0.5; cursor: not-allowed;">
                    <i class="fas fa-user-cog"></i>
                    <span>User Management</span>
                    <small class="text-muted d-block">Admin Only</small>
                </div>
            </div>
            @endcan
            
            @can('manage_settings')
            <div class="col-md-4">
                <a href="{{ route('settings.index') }}" class="shortcut-link">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </div>
            @else
            <div class="col-md-4">
                <div class="shortcut-link disabled" style="opacity: 0.5; cursor: not-allowed;">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                    <small class="text-muted d-block">Admin Only</small>
                </div>
            </div>
            @endcan
        </div>
    </div>
    @endauth
    
    @guest
    <div class="login-prompt">
        <h5>Not Logged In?</h5>
        <p>You need to be logged in to access this resource.</p>
        <a href="{{ route('login') }}" class="btn btn-primary">
            <i class="fas fa-sign-in-alt"></i> Login
        </a>
    </div>
    @endguest
</div>
@endsection 