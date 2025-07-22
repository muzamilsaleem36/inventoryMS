<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller;
use App\Models\User;
use App\Models\Setting;
use App\Models\Store;

class FirstTimeSetupController extends Controller
{
    /**
     * Show the auto-setup page
     */
    public function index()
    {
        $status = $this->checkSystemStatus();
        return view('auto-setup.index', compact('status'));
    }

    /**
     * Handle database configuration
     */
    public function configureDatabase(Request $request)
    {
        $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|integer|min:1|max:65535',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        try {
            // Update database configuration
            $this->updateDatabaseConfig($request->all());

            // Test database connection
            Config::set('database.connections.mysql.host', $request->db_host);
            Config::set('database.connections.mysql.port', $request->db_port);
            Config::set('database.connections.mysql.database', $request->db_database);
            Config::set('database.connections.mysql.username', $request->db_username);
            Config::set('database.connections.mysql.password', $request->db_password);

            // Reconnect to database
            DB::purge('mysql');
            DB::reconnect('mysql');

            // Test connection
            DB::connection()->getPdo();

            return response()->json(['success' => true, 'message' => 'Database connection successful']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Fallback to SQLite if MySQL is not available
     */
    public function fallbackToSQLite()
    {
        try {
            // Update environment to use SQLite
            $this->updateEnvironmentValue('DB_CONNECTION', 'sqlite');
            $this->updateEnvironmentValue('DB_DATABASE', database_path('database.sqlite'));
            
            // Create SQLite database file if it doesn't exist
            $sqliteFile = database_path('database.sqlite');
            if (!file_exists($sqliteFile)) {
                touch($sqliteFile);
            }

            // Set configuration for SQLite
            Config::set('database.default', 'sqlite');
            Config::set('database.connections.sqlite.database', $sqliteFile);

            // Reconnect to SQLite
            DB::purge('sqlite');
            DB::reconnect('sqlite');

            // Test connection
            DB::connection()->getPdo();

            return response()->json(['success' => true, 'message' => 'SQLite database configured successfully']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'SQLite setup failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Initialize database with migrations and seeds
     */
    public function initializeDatabase()
    {
        try {
            // Check if .env file exists, if not create it
            if (!file_exists(base_path('.env'))) {
                if (file_exists(base_path('env-xampp-ready.txt'))) {
                    copy(base_path('env-xampp-ready.txt'), base_path('.env'));
                } else {
                    copy(base_path('env-template.txt'), base_path('.env'));
                }
            }

            // Generate application key if not set
            if (empty(env('APP_KEY'))) {
                Artisan::call('key:generate', ['--force' => true]);
            }

            // Run migrations
            Artisan::call('migrate', ['--force' => true]);

            // Create storage symlink
            if (!file_exists(public_path('storage'))) {
                Artisan::call('storage:link');
            }

            // Create default settings
            $this->createDefaultSettings();

            // Create default store
            $this->createDefaultStore();

            return response()->json(['success' => true, 'message' => 'Database initialized successfully']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Database initialization failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Create admin user and complete setup
     */
    public function completeSetup(Request $request)
    {
        $request->validate([
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|min:8|confirmed',
            'shop_name' => 'required|string|max:255',
        ]);

        try {
            // Create admin user
            $admin = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // Update shop settings
            Setting::updateOrCreate(
                ['key' => 'shop_name'],
                ['value' => $request->shop_name]
            );

            Setting::updateOrCreate(
                ['key' => 'shop_email'],
                ['value' => $request->admin_email]
            );

            // Update environment
            $this->updateEnvironmentValue('APP_NAME', '"' . $request->shop_name . '"');
            $this->updateEnvironmentValue('SETUP_COMPLETED', 'true');
            $this->updateEnvironmentValue('FIRST_TIME_SETUP', 'false');

            return response()->json([
                'success' => true,
                'message' => 'Setup completed successfully!',
                'redirect' => route('login')
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Setup completion failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Check system status
     */
    private function checkSystemStatus()
    {
        $status = [
            'php_version' => PHP_VERSION,
            'php_extensions' => [],
            'database_connection' => false,
            'tables_exist' => false,
            'env_file_exists' => file_exists(base_path('.env')),
            'app_key_set' => !empty(env('APP_KEY')),
            'storage_linked' => file_exists(public_path('storage')),
            'writable_directories' => [],
        ];

        // Check PHP extensions
        $required_extensions = ['pdo', 'pdo_mysql', 'openssl', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath'];
        foreach ($required_extensions as $extension) {
            $status['php_extensions'][$extension] = extension_loaded($extension);
        }

        // Check database connection
        try {
            DB::connection()->getPdo();
            $status['database_connection'] = true;

            // Check if tables exist
            if (Schema::hasTable('users') && Schema::hasTable('settings') && Schema::hasTable('stores')) {
                $status['tables_exist'] = true;
            }
        } catch (\Exception $e) {
            $status['database_connection'] = false;
        }

        // Check writable directories
        $writable_dirs = ['storage', 'bootstrap/cache', 'storage/app', 'storage/framework', 'storage/logs'];
        foreach ($writable_dirs as $dir) {
            $path = base_path($dir);
            $status['writable_directories'][$dir] = is_writable($path);
        }

        return $status;
    }

    /**
     * Update database configuration in .env file
     */
    private function updateDatabaseConfig($config)
    {
        $env_updates = [
            'DB_HOST' => $config['db_host'],
            'DB_PORT' => $config['db_port'],
            'DB_DATABASE' => $config['db_database'],
            'DB_USERNAME' => $config['db_username'],
            'DB_PASSWORD' => $config['db_password'] ?? '',
        ];

        foreach ($env_updates as $key => $value) {
            $this->updateEnvironmentValue($key, $value);
        }
    }

    /**
     * Create default settings
     */
    private function createDefaultSettings()
    {
        $default_settings = [
            'shop_name' => 'My POS Shop',
            'shop_email' => 'admin@pos-system.local',
            'shop_phone' => '',
            'shop_address' => '',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'tax_rate' => '0.00',
            'receipt_header' => 'Thank you for your purchase!',
            'receipt_footer' => 'Made by Conzec Technologies. Contact WhatsApp +923325223746',
            'low_stock_threshold' => '10',
            'backup_enabled' => 'true',
            'backup_frequency' => 'daily',
            'theme' => 'default',
            'timezone' => 'UTC',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i:s',
        ];

        foreach ($default_settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    /**
     * Create default store
     */
    private function createDefaultStore()
    {
        if (!Store::exists()) {
            Store::create([
                'name' => 'Main Store',
                'address' => '',
                'phone' => '',
                'email' => '',
                'is_active' => true,
            ]);
        }
    }

    /**
     * Update environment value
     */
    private function updateEnvironmentValue($key, $value)
    {
        $envFile = base_path('.env');
        
        if (file_exists($envFile)) {
            $env = file_get_contents($envFile);
            
            // Update existing key
            if (strpos($env, $key . '=') !== false) {
                $env = preg_replace('/^' . $key . '=.*/m', $key . '=' . $value, $env);
            } else {
                // Add new key
                $env .= "\n" . $key . '=' . $value;
            }
            
            file_put_contents($envFile, $env);
        }
    }
} 