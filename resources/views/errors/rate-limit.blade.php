@extends('layouts.error')

@section('title', 'Too Many Requests')

@section('content')
<div class="text-center">
    <div class="mb-4">
        <i class="fas fa-hourglass-half fa-5x text-warning"></i>
    </div>
    <h1 class="display-4 mb-3">Too Many Requests</h1>
    <p class="lead mb-4">You have made too many requests. Please wait before trying again.</p>
    
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Rate Limit Exceeded</h5>
                    <p class="card-text">
                        To protect our system and ensure fair usage for all users, we have implemented rate limiting.
                        You have exceeded the maximum number of requests allowed in the current time window.
                    </p>
                    @if(isset($retry_after))
                    <p class="card-text">
                        <strong>Please wait {{ $retry_after }} seconds before trying again.</strong>
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <button class="btn btn-primary" onclick="window.location.reload()">
            <i class="fas fa-sync-alt"></i> Try Again
        </button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-home"></i> Go to Dashboard
        </a>
    </div>
</div>

@if(isset($retry_after))
<script>
// Auto-refresh after retry time
setTimeout(function() {
    window.location.reload();
}, {{ $retry_after * 1000 }});
</script>
@endif
@endsection 