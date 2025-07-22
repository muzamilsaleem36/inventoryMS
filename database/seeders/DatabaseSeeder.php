<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Store;
use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Setting;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $managerRole = Role::create(['name' => 'manager']);
        $cashierRole = Role::create(['name' => 'cashier']);

        // Create permissions
        $permissions = [
            'view_dashboard',
            'manage_users',
            'manage_stores',
            'manage_products',
            'manage_categories',
            'manage_customers',
            'manage_suppliers',
            'manage_sales',
            'view_sales',
            'manage_purchases',
            'view_reports',
            'manage_settings',
            'manage_expenses',
            'view_activity_logs',
            'generate_barcodes',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole->givePermissionTo($permissions);
        $managerRole->givePermissionTo([
            'view_dashboard',
            'manage_products',
            'manage_categories',
            'manage_customers',
            'manage_suppliers',
            'manage_sales',
            'manage_purchases',
            'view_reports',
            'generate_barcodes',
        ]);
        $cashierRole->givePermissionTo([
            'view_dashboard',
            'manage_customers',
            'manage_sales',
        ]);

        // Create default store
        $store = Store::create([
            'name' => 'Main Store',
            'code' => 'MAIN',
            'address' => '123 Main Street, City, State 12345',
            'phone' => '+1-555-0123',
            'email' => 'store@example.com',
            'manager_name' => 'Store Manager',
            'is_active' => true,
        ]);

        // Create admin user
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@pos.com',
            'password' => Hash::make('password'),
            'phone' => '+1-555-0100',
            'address' => '123 Admin Street',
            'is_active' => true,
            'store_id' => $store->id,
        ]);
        $admin->assignRole('admin');

        // Create manager user
        $manager = User::create([
            'name' => 'Store Manager',
            'email' => 'manager@pos.com',
            'password' => Hash::make('password'),
            'phone' => '+1-555-0101',
            'address' => '123 Manager Street',
            'is_active' => true,
            'store_id' => $store->id,
        ]);
        $manager->assignRole('manager');

        // Create cashier user
        $cashier = User::create([
            'name' => 'Cashier One',
            'email' => 'cashier@pos.com',
            'password' => Hash::make('password'),
            'phone' => '+1-555-0102',
            'address' => '123 Cashier Street',
            'is_active' => true,
            'store_id' => $store->id,
        ]);
        $cashier->assignRole('cashier');

        // Create categories
        $categories = [
            ['name' => 'Electronics', 'code' => 'ELEC', 'description' => 'Electronic devices and accessories'],
            ['name' => 'Clothing', 'code' => 'CLOT', 'description' => 'Clothing and apparel'],
            ['name' => 'Food & Beverages', 'code' => 'FOOD', 'description' => 'Food items and beverages'],
            ['name' => 'Books', 'code' => 'BOOK', 'description' => 'Books and magazines'],
            ['name' => 'Home & Garden', 'code' => 'HOME', 'description' => 'Home and garden items'],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        // Create sample products
        $products = [
            [
                'name' => 'Smartphone',
                'code' => 'PHONE001',
                'barcode' => '1234567890123',
                'description' => 'Latest model smartphone',
                'category_id' => 1,
                'purchase_price' => 300.00,
                'selling_price' => 599.99,
                'stock_quantity' => 25,
                'min_stock_level' => 5,
                'unit' => 'pcs',
                'store_id' => $store->id,
            ],
            [
                'name' => 'T-Shirt',
                'code' => 'TSHIRT001',
                'barcode' => '1234567890124',
                'description' => 'Cotton t-shirt',
                'category_id' => 2,
                'purchase_price' => 8.00,
                'selling_price' => 19.99,
                'stock_quantity' => 50,
                'min_stock_level' => 10,
                'unit' => 'pcs',
                'store_id' => $store->id,
            ],
            [
                'name' => 'Coffee Beans',
                'code' => 'COFFEE001',
                'barcode' => '1234567890125',
                'description' => 'Premium coffee beans',
                'category_id' => 3,
                'purchase_price' => 12.00,
                'selling_price' => 24.99,
                'stock_quantity' => 30,
                'min_stock_level' => 5,
                'unit' => 'kg',
                'store_id' => $store->id,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Create sample customers
        $customers = [
            [
                'name' => 'John Smith',
                'email' => 'john@example.com',
                'phone' => '+1-555-0200',
                'address' => '456 Customer Street',
                'credit_limit' => 1000.00,
            ],
            [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'phone' => '+1-555-0201',
                'address' => '789 Customer Avenue',
                'credit_limit' => 500.00,
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }

        // Create sample suppliers
        $suppliers = [
            [
                'name' => 'Tech Supplier',
                'company_name' => 'Tech Supply Co.',
                'email' => 'supplier@techsupply.com',
                'phone' => '+1-555-0300',
                'address' => '123 Supplier Street',
                'tax_number' => 'TAX123456',
                'credit_limit' => 10000.00,
            ],
            [
                'name' => 'Fashion Supplier',
                'company_name' => 'Fashion Supply Inc.',
                'email' => 'supplier@fashionsupply.com',
                'phone' => '+1-555-0301',
                'address' => '456 Supplier Avenue',
                'tax_number' => 'TAX789012',
                'credit_limit' => 5000.00,
            ],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::create($supplierData);
        }

        // Create default settings
        $settings = [
            ['key' => 'business_name', 'value' => 'Professional POS System', 'group' => 'business'],
            ['key' => 'business_address', 'value' => '123 Business Street, City, State 12345', 'group' => 'business'],
            ['key' => 'business_phone', 'value' => '+1-555-0123', 'group' => 'business'],
            ['key' => 'business_email', 'value' => 'info@yourpos.com', 'group' => 'business'],
            ['key' => 'tax_rate', 'value' => '0.00', 'group' => 'business'],
            ['key' => 'currency', 'value' => 'USD', 'group' => 'business'],
            ['key' => 'receipt_footer', 'value' => 'Thank you for your business!', 'group' => 'business'],
            ['key' => 'low_stock_alert', 'value' => '1', 'group' => 'inventory'],
            ['key' => 'barcode_enabled', 'value' => '1', 'group' => 'features'],
            ['key' => 'multi_store_enabled', 'value' => '0', 'group' => 'features'],
        ];

        foreach ($settings as $settingData) {
            Setting::create($settingData);
        }
    }
} 