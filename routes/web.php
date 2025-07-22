<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    Auth\AuthController,
    DashboardController,
    ProductController,
    CategoryController,
    CustomerController,
    SupplierController,
    SaleController,
    PurchaseController,
    ReportController,
    SettingController,
    UserController,
    StoreController,
    BarcodeController,
    ExpenseController,
    SetupController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Setup routes - must be before auth middleware
Route::get('/setup', [SetupController::class, 'index'])->name('setup.index');
Route::post('/setup', [SetupController::class, 'store'])->name('setup.store');

// Authentication Routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth', 'check.setup'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Product Management
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('products.adjust-stock');
    
    // Category Management
    Route::resource('categories', CategoryController::class);
    
    // Customer Management
    Route::resource('customers', CustomerController::class);
    
    // Supplier Management
    Route::resource('suppliers', SupplierController::class);
    
    // Sales Management
    Route::resource('sales', SaleController::class);
    Route::get('sales/pos', [SaleController::class, 'pos'])->name('sales.pos');
    Route::get('sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
    Route::get('sales/{sale}/print', [SaleController::class, 'printReceipt'])->name('sales.print');
    
    // Purchase Management
    Route::resource('purchases', PurchaseController::class);
    Route::get('purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
    Route::post('purchases/{purchase}/receive', [PurchaseController::class, 'processReceive'])->name('purchases.process-receive');
    Route::get('purchases/{purchase}/print', [PurchaseController::class, 'print'])->name('purchases.print');
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('products', [ReportController::class, 'products'])->name('products');
        Route::get('customers', [ReportController::class, 'customers'])->name('customers');
        Route::get('stock', [ReportController::class, 'stock'])->name('stock');
        Route::get('expenses', [ReportController::class, 'expenses'])->name('expenses');
    });
    
    // User Management (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        
        // Store Management (Premium Feature)
        Route::resource('stores', StoreController::class);
        
        // Settings
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
        
        // Expenses (Premium Feature)
        Route::resource('expenses', ExpenseController::class);
    });
    
    // Barcode Generation (Premium Feature)
    Route::middleware('role:admin|manager')->group(function () {
        Route::get('barcodes', [BarcodeController::class, 'index'])->name('barcodes.index');
        Route::get('barcodes/generate/{product}', [BarcodeController::class, 'generate'])->name('barcodes.generate');
        Route::post('barcodes/batch-generate', [BarcodeController::class, 'batchGenerate'])->name('barcodes.batch-generate');
    });
}); 