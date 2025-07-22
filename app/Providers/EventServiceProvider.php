<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // POS System Events
        'App\Events\SaleCompleted' => [
            'App\Listeners\UpdateInventory',
            'App\Listeners\GenerateReceipt',
            'App\Listeners\LogSaleActivity',
        ],

        'App\Events\ProductCreated' => [
            'App\Listeners\GenerateProductBarcode',
            'App\Listeners\LogProductActivity',
        ],

        'App\Events\ProductUpdated' => [
            'App\Listeners\UpdateProductBarcode',
            'App\Listeners\LogProductActivity',
        ],

        'App\Events\StockLevelChanged' => [
            'App\Listeners\CheckLowStockAlert',
            'App\Listeners\LogInventoryActivity',
        ],

        'App\Events\CustomerCreated' => [
            'App\Listeners\SendWelcomeEmail',
            'App\Listeners\LogCustomerActivity',
        ],

        'App\Events\UserLogin' => [
            'App\Listeners\LogLoginActivity',
            'App\Listeners\UpdateLastLogin',
        ],

        'App\Events\UserLogout' => [
            'App\Listeners\LogLogoutActivity',
        ],

        'App\Events\PaymentProcessed' => [
            'App\Listeners\GeneratePaymentReceipt',
            'App\Listeners\LogPaymentActivity',
        ],

        'App\Events\PurchaseReceived' => [
            'App\Listeners\UpdateInventoryFromPurchase',
            'App\Listeners\LogPurchaseActivity',
        ],

        'App\Events\BackupCompleted' => [
            'App\Listeners\NotifyBackupStatus',
            'App\Listeners\LogBackupActivity',
        ],

        'App\Events\SystemMaintenanceStarted' => [
            'App\Listeners\NotifyMaintenanceUsers',
            'App\Listeners\LogMaintenanceActivity',
        ],

        'App\Events\LowStockAlert' => [
            'App\Listeners\SendLowStockNotification',
            'App\Listeners\LogStockAlert',
        ],

        'App\Events\ExpenseCreated' => [
            'App\Listeners\LogExpenseActivity',
        ],

        'App\Events\ReportGenerated' => [
            'App\Listeners\LogReportActivity',
        ],

        'App\Events\SettingsUpdated' => [
            'App\Listeners\ClearSettingsCache',
            'App\Listeners\LogSettingsActivity',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Model events
        Event::listen('eloquent.created: App\Models\Sale', function ($sale) {
            event(new \App\Events\SaleCompleted($sale));
        });

        Event::listen('eloquent.created: App\Models\Product', function ($product) {
            event(new \App\Events\ProductCreated($product));
        });

        Event::listen('eloquent.updated: App\Models\Product', function ($product) {
            event(new \App\Events\ProductUpdated($product));
        });

        Event::listen('eloquent.updated: App\Models\Product', function ($product) {
            if ($product->isDirty('stock_quantity')) {
                event(new \App\Events\StockLevelChanged($product));
            }
        });

        Event::listen('eloquent.created: App\Models\Customer', function ($customer) {
            event(new \App\Events\CustomerCreated($customer));
        });

        Event::listen('eloquent.created: App\Models\Purchase', function ($purchase) {
            event(new \App\Events\PurchaseReceived($purchase));
        });

        Event::listen('eloquent.created: App\Models\Expense', function ($expense) {
            event(new \App\Events\ExpenseCreated($expense));
        });

        Event::listen('eloquent.updated: App\Models\Setting', function ($setting) {
            event(new \App\Events\SettingsUpdated($setting));
        });

        // Authentication events
        Event::listen('Illuminate\Auth\Events\Login', function ($event) {
            event(new \App\Events\UserLogin($event->user));
        });

        Event::listen('Illuminate\Auth\Events\Logout', function ($event) {
            event(new \App\Events\UserLogout($event->user));
        });

        // Custom POS events
        Event::listen('pos.payment.processed', function ($payment) {
            event(new \App\Events\PaymentProcessed($payment));
        });

        Event::listen('pos.backup.completed', function ($backup) {
            event(new \App\Events\BackupCompleted($backup));
        });

        Event::listen('pos.maintenance.started', function () {
            event(new \App\Events\SystemMaintenanceStarted());
        });

        Event::listen('pos.stock.low', function ($product) {
            event(new \App\Events\LowStockAlert($product));
        });

        Event::listen('pos.report.generated', function ($report) {
            event(new \App\Events\ReportGenerated($report));
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
} 