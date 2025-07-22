<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SetupController extends Controller
{
    public function index()
    {
        // Check if setup is already completed
        if ($this->isSetupComplete()) {
            return redirect()->route('dashboard');
        }

        return view('setup.index');
    }

    public function store(Request $request)
    {
        // Validate the setup form
        $validator = Validator::make($request->all(), [
            'shop_name' => 'required|string|max:255',
            'shop_address' => 'required|string|max:500',
            'shop_phone' => 'required|string|max:20',
            'shop_email' => 'required|email|max:255',
            'shop_website' => 'nullable|url|max:255',
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|max:255',
            'owner_phone' => 'required|string|max:20',
            'timezone' => 'required|string|max:50',
            'currency' => 'required|string|max:10',
            'currency_symbol' => 'required|string|max:5',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'tax_number' => 'nullable|string|max:50',
            'receipt_format' => 'nullable|string|in:80mm,58mm,a4',
            'receipt_footer' => 'nullable|string|max:255',
            'barcode_format' => 'nullable|string|in:CODE128,CODE39,EAN13,EAN8',
            'low_stock_threshold' => 'nullable|integer|min:1|max:100',
            'date_format' => 'nullable|string|max:20',
            'time_format' => 'nullable|string|max:20',
            'auto_print_receipt' => 'boolean',
            'track_inventory' => 'boolean',
            'email_notifications' => 'boolean',
            'low_stock_alerts' => 'boolean',
            'daily_sales_report' => 'boolean',
            'shop_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            // Create roles and permissions if they don't exist
            $this->createRolesAndPermissions();

            // Handle logo upload
            $logoPath = null;
            if ($request->hasFile('shop_logo')) {
                $logoPath = $request->file('shop_logo')->store('logos', 'public');
            }

            // Create default store
            $store = Store::create([
                'name' => $request->shop_name,
                'address' => $request->shop_address,
                'phone' => $request->shop_phone,
                'email' => $request->shop_email,
                'website' => $request->shop_website,
                'logo' => $logoPath,
                'is_active' => true,
            ]);

            // Create admin user
            $adminUser = User::create([
                'name' => $request->owner_name,
                'email' => $request->owner_email,
                'phone' => $request->owner_phone,
                'password' => Hash::make($request->admin_password),
                'store_id' => $store->id,
                'is_active' => true,
            ]);

            // Assign admin role
            $adminUser->assignRole('admin');

            // Create comprehensive system settings
            $settings = [
                // Business Information
                'shop_name' => $request->shop_name,
                'shop_address' => $request->shop_address,
                'shop_phone' => $request->shop_phone,
                'shop_email' => $request->shop_email,
                'shop_website' => $request->shop_website,
                'owner_name' => $request->owner_name,
                'owner_email' => $request->owner_email,
                'owner_phone' => $request->owner_phone,
                'shop_logo' => $logoPath,
                
                // Financial Settings
                'currency' => $request->currency,
                'currency_symbol' => $request->currency_symbol,
                'tax_rate' => $request->tax_rate,
                'tax_number' => $request->tax_number,
                
                // System Settings
                'timezone' => $request->timezone,
                'date_format' => $request->date_format ?? 'Y-m-d',
                'time_format' => $request->time_format ?? 'H:i',
                
                // POS Preferences
                'receipt_format' => $request->receipt_format ?? '80mm',
                'receipt_footer' => $request->receipt_footer ?? 'Thank you for your business!',
                'barcode_format' => $request->barcode_format ?? 'CODE128',
                'low_stock_threshold' => $request->low_stock_threshold ?? 10,
                'auto_print_receipt' => $request->boolean('auto_print_receipt', false),
                'track_inventory' => $request->boolean('track_inventory', true),
                
                // Notification Settings
                'email_notifications' => $request->boolean('email_notifications', true),
                'low_stock_alerts' => $request->boolean('low_stock_alerts', true),
                'daily_sales_report' => $request->boolean('daily_sales_report', false),
                
                // System Status
                'setup_completed' => true,
                'installation_date' => now(),
                'default_store_id' => $store->id,
                
                // Branding
                'developer_credit' => 'Made by Conzec Technologies',
                'whatsapp_contact' => '+923325223746',
                'system_version' => '1.0.0',
                
                // Additional Settings
                'backup_retention_days' => 30,
                'session_timeout' => 120,
                'max_login_attempts' => 5,
                'enable_multi_store' => false,
                'enable_barcode_scanner' => true,
                'enable_customer_display' => false,
                'enable_kitchen_display' => false,
            ];

            foreach ($settings as $key => $value) {
                Setting::create([
                    'key' => $key,
                    'value' => $value,
                    'type' => $this->getSettingType($value),
                    'store_id' => $store->id,
                ]);
            }

            DB::commit();

            // Log the user in
            auth()->login($adminUser);

            return redirect()->route('dashboard')->with('success', 'Setup completed successfully! Welcome to your POS system.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded logo if exists
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }

            return back()->withError('Setup failed. Please try again. Error: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Create roles and permissions for the system
     */
    private function createRolesAndPermissions()
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $cashierRole = Role::firstOrCreate(['name' => 'cashier']);

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
            Permission::firstOrCreate(['name' => $permission]);
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
    }

    private function isSetupComplete()
    {
        return Setting::where('key', 'setup_completed')->where('value', true)->exists();
    }

    private function getSettingType($value)
    {
        if (is_bool($value)) {
            return 'boolean';
        } elseif (is_numeric($value)) {
            return 'number';
        } elseif (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        } elseif (filter_var($value, FILTER_VALIDATE_URL)) {
            return 'url';
        } else {
            return 'text';
        }
    }
} 