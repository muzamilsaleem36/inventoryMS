<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('pos:status', function () {
    $this->info('POS System Status Check');
    $this->line('------------------------');
    
    // Check database connection
    try {
        DB::connection()->getPdo();
        $this->info('✓ Database connection: OK');
    } catch (Exception $e) {
        $this->error('✗ Database connection: FAILED');
    }
    
    // Check storage permissions
    if (is_writable(storage_path())) {
        $this->info('✓ Storage permissions: OK');
    } else {
        $this->error('✗ Storage permissions: FAILED');
    }
    
    // Check environment
    $this->info('✓ Environment: ' . config('app.env'));
    $this->info('✓ Debug mode: ' . (config('app.debug') ? 'ON' : 'OFF'));
    
})->purpose('Check POS system status'); 