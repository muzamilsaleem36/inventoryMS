@extends('layouts.error')

@section('title', 'System Under Maintenance')

@section('content')
<div class="text-center">
    <div class="mb-4">
        <i class="fas fa-tools fa-5x text-warning"></i>
    </div>
    <h1 class="display-4 mb-3">System Under Maintenance</h1>
    <p class="lead mb-4">{{ $message ?? 'We are currently performing system maintenance. Please try again later.' }}</p>
    
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">What's happening?</h5>
                    <p class="card-text">
                        Our system is currently undergoing scheduled maintenance to improve performance and add new features. 
                        This process is temporary and should be completed soon.
                    </p>
                    <p class="card-text">
                        <small class="text-muted">
                            We apologize for any inconvenience this may cause. Please try again in a few minutes.
                        </small>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <button class="btn btn-primary" onclick="window.location.reload()">
            <i class="fas fa-sync-alt"></i> Try Again
        </button>
    </div>
</div>

<script>
// Auto-refresh every 30 seconds
setTimeout(function() {
    window.location.reload();
}, 30000);
</script>
@endsection 