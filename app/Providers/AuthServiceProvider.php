<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Category;
use App\Models\Store;
use App\Models\Expense;
use App\Models\Setting;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Super admin can do everything
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });

        // Define gates for POS system operations
        Gate::define('view-dashboard', function (User $user) {
            return $user->hasPermissionTo('view_dashboard');
        });

        Gate::define('manage-users', function (User $user) {
            return $user->hasPermissionTo('manage_users');
        });

        Gate::define('manage-stores', function (User $user) {
            return $user->hasPermissionTo('manage_stores');
        });

        Gate::define('manage-products', function (User $user) {
            return $user->hasPermissionTo('manage_products');
        });

        Gate::define('manage-categories', function (User $user) {
            return $user->hasPermissionTo('manage_categories');
        });

        Gate::define('manage-customers', function (User $user) {
            return $user->hasPermissionTo('manage_customers');
        });

        Gate::define('manage-suppliers', function (User $user) {
            return $user->hasPermissionTo('manage_suppliers');
        });

        Gate::define('manage-sales', function (User $user) {
            return $user->hasPermissionTo('manage_sales');
        });

        Gate::define('view-sales', function (User $user) {
            return $user->hasPermissionTo('view_sales') || $user->hasPermissionTo('manage_sales');
        });

        Gate::define('manage-purchases', function (User $user) {
            return $user->hasPermissionTo('manage_purchases');
        });

        Gate::define('view-reports', function (User $user) {
            return $user->hasPermissionTo('view_reports');
        });

        Gate::define('manage-settings', function (User $user) {
            return $user->hasPermissionTo('manage_settings');
        });

        Gate::define('manage-expenses', function (User $user) {
            return $user->hasPermissionTo('manage_expenses');
        });

        Gate::define('view-activity-logs', function (User $user) {
            return $user->hasPermissionTo('view_activity_logs');
        });

        Gate::define('generate-barcodes', function (User $user) {
            return $user->hasPermissionTo('generate_barcodes');
        });

        // Store-specific permissions
        Gate::define('manage-own-store-only', function (User $user) {
            return $user->hasRole('cashier') || $user->hasRole('manager');
        });

        Gate::define('view-own-store-data', function (User $user, $model) {
            if ($user->hasRole('admin')) {
                return true;
            }
            
            if ($user->store_id && method_exists($model, 'store_id')) {
                return $user->store_id === $model->store_id;
            }
            
            return false;
        });

        // Product-specific permissions
        Gate::define('update-product-stock', function (User $user, Product $product) {
            return $user->hasPermissionTo('manage_products') && 
                   ($user->hasRole('admin') || $user->store_id === $product->store_id);
        });

        // Sale-specific permissions
        Gate::define('process-sale', function (User $user) {
            return $user->hasPermissionTo('manage_sales') && $user->is_active;
        });

        Gate::define('refund-sale', function (User $user, Sale $sale) {
            return $user->hasPermissionTo('manage_sales') && 
                   ($user->hasRole('admin') || $user->hasRole('manager'));
        });

        // Purchase-specific permissions
        Gate::define('approve-purchase', function (User $user, Purchase $purchase) {
            return $user->hasPermissionTo('manage_purchases') && 
                   ($user->hasRole('admin') || $user->hasRole('manager'));
        });

        // Customer-specific permissions
        Gate::define('manage-customer-credit', function (User $user, Customer $customer) {
            return $user->hasPermissionTo('manage_customers') && 
                   ($user->hasRole('admin') || $user->hasRole('manager'));
        });

        // Expense-specific permissions
        Gate::define('approve-expense', function (User $user, Expense $expense) {
            return $user->hasPermissionTo('manage_expenses') && 
                   ($user->hasRole('admin') || $user->hasRole('manager'));
        });

        // Settings-specific permissions
        Gate::define('modify-critical-settings', function (User $user) {
            return $user->hasPermissionTo('manage_settings') && $user->hasRole('admin');
        });

        // Report-specific permissions
        Gate::define('export-reports', function (User $user) {
            return $user->hasPermissionTo('view_reports') && 
                   ($user->hasRole('admin') || $user->hasRole('manager'));
        });

        // User management permissions
        Gate::define('manage-user-roles', function (User $user, User $targetUser) {
            return $user->hasPermissionTo('manage_users') && 
                   $user->hasRole('admin') && 
                   $user->id !== $targetUser->id;
        });

        Gate::define('deactivate-user', function (User $user, User $targetUser) {
            return $user->hasPermissionTo('manage_users') && 
                   $user->hasRole('admin') && 
                   !$targetUser->hasRole('super-admin') && 
                   $user->id !== $targetUser->id;
        });

        // Store management permissions
        Gate::define('manage-store-settings', function (User $user, Store $store) {
            return $user->hasPermissionTo('manage_stores') && 
                   ($user->hasRole('admin') || $user->store_id === $store->id);
        });

        // Activity log permissions
        Gate::define('view-user-activity', function (User $user, User $targetUser) {
            return $user->hasPermissionTo('view_activity_logs') && 
                   ($user->hasRole('admin') || $user->id === $targetUser->id);
        });

        // Barcode generation permissions
        Gate::define('bulk-generate-barcodes', function (User $user) {
            return $user->hasPermissionTo('generate_barcodes') && 
                   ($user->hasRole('admin') || $user->hasRole('manager'));
        });
    }
} 