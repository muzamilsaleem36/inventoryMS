<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProductController,
    CustomerController,
    SupplierController,
    SaleController,
    DashboardController,
    BarcodeController,
    SettingController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Authentication required for all API routes
Route::middleware('auth:sanctum')->group(function () {
    
    // Product API Routes
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('search', [ProductController::class, 'search'])->name('search');
        Route::get('{id}', [SaleController::class, 'getProduct'])->name('get');
        Route::get('{id}/details', [ProductController::class, 'getProductDetails'])->name('details');
        Route::post('{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('adjust-stock');
    });
    
    // Customer API Routes
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('search', [CustomerController::class, 'search'])->name('search');
        Route::get('{id}', [CustomerController::class, 'getCustomer'])->name('get');
        Route::get('{id}/purchases', [CustomerController::class, 'getPurchaseHistory'])->name('purchases');
    });
    
    // Supplier API Routes
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('search', [SupplierController::class, 'search'])->name('search');
        Route::get('{id}', [SupplierController::class, 'getSupplier'])->name('get');
        Route::get('{id}/products', [SupplierController::class, 'getSupplierProducts'])->name('products');
    });
    
    // Sales API Routes
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('products/search', [SaleController::class, 'searchProducts'])->name('products.search');
        Route::get('{sale}/receipt', [SaleController::class, 'getReceiptData'])->name('receipt');
        Route::post('calculate-total', [SaleController::class, 'calculateTotal'])->name('calculate-total');
    });
    
    // Purchase API Routes
    Route::prefix('purchases')->name('purchases.')->group(function () {
        Route::get('{purchase}/details', [PurchaseController::class, 'getPurchaseDetails'])->name('details');
        Route::post('calculate-total', [PurchaseController::class, 'calculateTotal'])->name('calculate-total');
    });
    
    // Dashboard API Routes
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('stats', [DashboardController::class, 'stats'])->name('stats');
        Route::get('recent-sales', [DashboardController::class, 'getRecentSales'])->name('recent-sales');
        Route::get('low-stock', [DashboardController::class, 'getLowStockProducts'])->name('low-stock');
        Route::get('top-products', [DashboardController::class, 'getTopProducts'])->name('top-products');
    });
    
    // Barcode API Routes
    Route::middleware('role:admin|manager')->group(function () {
        Route::prefix('barcodes')->name('barcodes.')->group(function () {
            Route::post('validate', [BarcodeController::class, 'validateBarcode'])->name('validate');
            Route::post('generate', [BarcodeController::class, 'generateBarcode'])->name('generate');
            Route::post('bulk-generate', [BarcodeController::class, 'bulkGenerate'])->name('bulk-generate');
        });
    });
    
    // Settings API Routes (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingController::class, 'getSettings'])->name('get');
            Route::post('/', [SettingController::class, 'updateSettings'])->name('update');
            Route::delete('logo', [SettingController::class, 'deleteLogo'])->name('delete-logo');
        });
    });
    
    // Report API Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('sales-data', [ReportController::class, 'getSalesData'])->name('sales-data');
        Route::get('product-data', [ReportController::class, 'getProductData'])->name('product-data');
        Route::get('stock-data', [ReportController::class, 'getStockData'])->name('stock-data');
        Route::get('expense-data', [ReportController::class, 'getExpenseData'])->name('expense-data');
    });
    
    // User API Routes (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('search', [UserController::class, 'search'])->name('search');
            Route::post('{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('{user}/activity', [UserController::class, 'getActivity'])->name('activity');
        });
    });
    
    // Store API Routes (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::prefix('stores')->name('stores.')->group(function () {
            Route::get('search', [StoreController::class, 'search'])->name('search');
            Route::get('{store}/stats', [StoreController::class, 'getStoreStats'])->name('stats');
            Route::get('{store}/users', [StoreController::class, 'getStoreUsers'])->name('users');
        });
    });
    
    // Expense API Routes (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::prefix('expenses')->name('expenses.')->group(function () {
            Route::get('categories', [ExpenseController::class, 'getCategories'])->name('categories');
            Route::post('{expense}/approve', [ExpenseController::class, 'approve'])->name('approve');
            Route::post('{expense}/reject', [ExpenseController::class, 'reject'])->name('reject');
        });
    });
    
    // Category API Routes
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('search', [CategoryController::class, 'search'])->name('search');
        Route::get('{category}/products', [CategoryController::class, 'getProducts'])->name('products');
        Route::get('tree', [CategoryController::class, 'getCategoryTree'])->name('tree');
    });
    
    // File Upload API Routes
    Route::prefix('upload')->name('upload.')->group(function () {
        Route::post('image', [UploadController::class, 'uploadImage'])->name('image');
        Route::post('document', [UploadController::class, 'uploadDocument'])->name('document');
        Route::delete('file/{filename}', [UploadController::class, 'deleteFile'])->name('delete-file');
    });
    
    // Utility API Routes
    Route::prefix('utils')->name('utils.')->group(function () {
        Route::get('currencies', [UtilityController::class, 'getCurrencies'])->name('currencies');
        Route::get('timezones', [UtilityController::class, 'getTimezones'])->name('timezones');
        Route::get('countries', [UtilityController::class, 'getCountries'])->name('countries');
    });
});

// Public API Routes (no authentication required)
Route::prefix('public')->name('public.')->group(function () {
    Route::get('store-info', [PublicController::class, 'getStoreInfo'])->name('store-info');
    Route::get('business-hours', [PublicController::class, 'getBusinessHours'])->name('business-hours');
});

// Webhook Routes (for external integrations)
Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::post('payment-confirmation', [WebhookController::class, 'paymentConfirmation'])->name('payment-confirmation');
    Route::post('inventory-update', [WebhookController::class, 'inventoryUpdate'])->name('inventory-update');
});

// Health Check Route
Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => config('app.version', '1.0.0')
    ]);
})->name('health');

// Rate Limited Routes
Route::middleware('throttle:60,1')->group(function () {
    Route::post('contact', [ContactController::class, 'store'])->name('contact');
    Route::post('feedback', [FeedbackController::class, 'store'])->name('feedback');
}); 