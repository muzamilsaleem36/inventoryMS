<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Models\Setting;
use App\Models\UserActivityLog;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\User;
use App\Models\Store;
use Carbon\Carbon;
use ZipArchive;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:backup-database 
                            {--path= : Custom backup path}
                            {--compress : Compress the backup file}
                            {--clean : Clean old backups}
                            {--format=sql : Backup format (sql, xml, excel, full)}
                            {--cloud : Upload to cloud storage}
                            {--tables= : Specific tables to backup (comma-separated)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the POS system database and business files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting comprehensive backup...');
        
        try {
            $format = $this->option('format');
            $compress = $this->option('compress');
            $cleanOld = $this->option('clean');
            $uploadCloud = $this->option('cloud');
            $specificTables = $this->option('tables');
            
            // Create backup directory if it doesn't exist
            if (!Storage::disk('local')->exists('backups')) {
                Storage::disk('local')->makeDirectory('backups');
            }
            
            // Generate backup based on format
            $backupFiles = [];
            
            switch ($format) {
                case 'sql':
                    $backupFiles[] = $this->createSQLBackup($specificTables);
                    break;
                case 'xml':
                    $backupFiles[] = $this->createXMLBackup($specificTables);
                    break;
                case 'excel':
                    $backupFiles[] = $this->createExcelBackup($specificTables);
                    break;
                case 'full':
                    $backupFiles[] = $this->createFullBackup($specificTables);
                    break;
                default:
                    throw new \Exception('Invalid backup format: ' . $format);
            }
            
            // Compress if requested
            if ($compress) {
                $backupFiles = $this->compressBackups($backupFiles);
            }
            
            // Upload to cloud if requested
            if ($uploadCloud) {
                $this->uploadToCloud($backupFiles);
            }
            
            // Clean old backups if requested
            if ($cleanOld) {
                $this->cleanOldBackups();
            }
            
            $this->info('Backup created successfully!');
            foreach ($backupFiles as $file) {
                $this->info('File: ' . $file);
                $this->info('Size: ' . $this->formatBytes(Storage::disk('local')->size('backups/' . $file)));
            }
            
            // Log the backup activity
            UserActivityLog::create([
                'user_id' => 1, // System user
                'action' => 'comprehensive_backup',
                'description' => 'Comprehensive backup created (' . $format . '): ' . implode(', ', $backupFiles),
                'ip_address' => '127.0.0.1'
            ]);
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('Backup failed: ' . $e->getMessage());
            Log::error('Comprehensive backup failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    /**
     * Get default backup path
     */
    protected function getDefaultBackupPath(): string
    {
        return storage_path('app/backups');
    }
    
    /**
     * Clean old backups based on retention policy
     */
    protected function cleanOldBackups(): void
    {
        $retentionDays = Setting::get('backup_retention_days', 30);
        $cutoffDate = Carbon::now()->subDays($retentionDays);
        
        $backupFiles = Storage::disk('local')->files('backups');
        
        foreach ($backupFiles as $file) {
            $lastModified = Storage::disk('local')->lastModified($file);
            
            if ($lastModified < $cutoffDate->timestamp) {
                Storage::disk('local')->delete($file);
                $this->info('Deleted old backup: ' . basename($file));
            }
        }
    }
    
    /**
     * Format bytes into human readable format
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Create SQL backup
     */
    protected function createSQLBackup($specificTables = null): string
    {
        $this->info('Creating SQL backup...');
        
        $filename = 'pos_backup_' . Carbon::now()->format('Y-m-d_H-i-s') . '.sql';
        $dbConfig = config('database.connections.' . config('database.default'));
        
        $tablesOption = '';
        if ($specificTables) {
            $tablesOption = ' ' . str_replace(',', ' ', $specificTables);
        }
        
        $command = sprintf(
            'mysqldump -h%s -u%s -p%s %s%s > %s',
            $dbConfig['host'],
            $dbConfig['username'],
            $dbConfig['password'],
            $dbConfig['database'],
            $tablesOption,
            storage_path('app/backups/' . $filename)
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception('SQL backup failed with return code: ' . $returnCode);
        }
        
        return $filename;
    }

    /**
     * Create XML backup
     */
    protected function createXMLBackup($specificTables = null): string
    {
        $this->info('Creating XML backup...');
        
        $filename = 'pos_backup_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xml';
        $filePath = storage_path('app/backups/' . $filename);
        
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        
        $root = $dom->createElement('pos_backup');
        $root->setAttribute('created_at', Carbon::now()->toISOString());
        $root->setAttribute('version', '1.0');
        $dom->appendChild($root);
        
        // Business info
        $businessInfo = $dom->createElement('business_info');
        $settings = Setting::all();
        foreach ($settings as $setting) {
            $element = $dom->createElement('setting');
            $element->setAttribute('key', $setting->key);
            $element->setAttribute('value', $setting->value);
            $businessInfo->appendChild($element);
        }
        $root->appendChild($businessInfo);
        
        // Tables to backup
        $tablesToBackup = $specificTables ? explode(',', $specificTables) : [
            'users', 'stores', 'categories', 'products', 'customers', 'suppliers',
            'sales', 'sale_items', 'purchases', 'purchase_items', 'expenses'
        ];
        
        foreach ($tablesToBackup as $table) {
            $this->addTableToXML($dom, $root, $table);
        }
        
        $dom->save($filePath);
        
        return $filename;
    }

    /**
     * Create Excel backup
     */
    protected function createExcelBackup($specificTables = null): string
    {
        $this->info('Creating Excel backup...');
        
        $filename = 'pos_backup_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';
        $filePath = storage_path('app/backups/' . $filename);
        
        // Create a simple CSV-based backup (can be opened by Excel)
        $csvData = [];
        
        // Business Information
        $csvData[] = ['Business Information'];
        $csvData[] = ['Key', 'Value'];
        $settings = Setting::all();
        foreach ($settings as $setting) {
            $csvData[] = [$setting->key, $setting->value];
        }
        $csvData[] = [''];
        
        // Tables to backup
        $tablesToBackup = $specificTables ? explode(',', $specificTables) : [
            'users', 'stores', 'categories', 'products', 'customers', 'suppliers',
            'sales', 'sale_items', 'purchases', 'purchase_items', 'expenses'
        ];
        
        foreach ($tablesToBackup as $table) {
            $this->addTableToCSV($csvData, $table);
        }
        
        // Save as CSV (Excel compatible)
        $handle = fopen($filePath, 'w');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
        
        return $filename;
    }

    /**
     * Create full backup with all files
     */
    protected function createFullBackup($specificTables = null): string
    {
        $this->info('Creating full system backup...');
        
        $filename = 'pos_full_backup_' . Carbon::now()->format('Y-m-d_H-i-s') . '.zip';
        $filePath = storage_path('app/backups/' . $filename);
        
        $zip = new ZipArchive();
        if ($zip->open($filePath, ZipArchive::CREATE) !== TRUE) {
            throw new \Exception('Cannot create zip file: ' . $filePath);
        }
        
        // Add SQL backup
        $sqlFile = $this->createSQLBackup($specificTables);
        $zip->addFile(storage_path('app/backups/' . $sqlFile), 'database/' . $sqlFile);
        
        // Add XML backup
        $xmlFile = $this->createXMLBackup($specificTables);
        $zip->addFile(storage_path('app/backups/' . $xmlFile), 'exports/' . $xmlFile);
        
        // Add configuration files
        $configFiles = [
            'config/app.php',
            'config/database.php',
            'config/mail.php',
            'config/services.php',
            '.env'
        ];
        
        foreach ($configFiles as $configFile) {
            $fullPath = base_path($configFile);
            if (File::exists($fullPath)) {
                $zip->addFile($fullPath, 'config/' . basename($configFile));
            }
        }
        
        // Add business files
        $businessDirs = [
            'storage/app/public',
            'public/uploads',
            'resources/views/layouts',
            'resources/views/auth'
        ];
        
        foreach ($businessDirs as $dir) {
            $fullPath = base_path($dir);
            if (File::exists($fullPath)) {
                $this->addDirToZip($zip, $fullPath, 'business/' . basename($dir));
            }
        }
        
        $zip->close();
        
        // Clean up temporary files
        Storage::disk('local')->delete('backups/' . $sqlFile);
        Storage::disk('local')->delete('backups/' . $xmlFile);
        
        return $filename;
    }

    /**
     * Add table data to XML
     */
    protected function addTableToXML(\DOMDocument $dom, \DOMElement $root, string $table): void
    {
        if (!DB::getSchemaBuilder()->hasTable($table)) {
            return;
        }
        
        $tableElement = $dom->createElement('table');
        $tableElement->setAttribute('name', $table);
        
        $data = DB::table($table)->get();
        
        foreach ($data as $row) {
            $rowElement = $dom->createElement('row');
            
            foreach ($row as $column => $value) {
                $columnElement = $dom->createElement('column');
                $columnElement->setAttribute('name', $column);
                $columnElement->setAttribute('value', $value ?? '');
                $rowElement->appendChild($columnElement);
            }
            
            $tableElement->appendChild($rowElement);
        }
        
        $root->appendChild($tableElement);
    }

    /**
     * Add table data to CSV
     */
    protected function addTableToCSV(array &$csvData, string $table): void
    {
        if (!DB::getSchemaBuilder()->hasTable($table)) {
            return;
        }
        
        $csvData[] = [strtoupper($table) . ' TABLE'];
        
        $data = DB::table($table)->get();
        
        if ($data->isNotEmpty()) {
            // Add headers
            $headers = array_keys((array) $data->first());
            $csvData[] = $headers;
            
            // Add data rows
            foreach ($data as $row) {
                $csvData[] = array_values((array) $row);
            }
        }
        
        $csvData[] = [''];
    }

    /**
     * Compress backup files
     */
    protected function compressBackups(array $backupFiles): array
    {
        $this->info('Compressing backups...');
        
        $compressedFiles = [];
        
        foreach ($backupFiles as $file) {
            $filePath = storage_path('app/backups/' . $file);
            
            if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                // Already compressed
                $compressedFiles[] = $file;
                continue;
            }
            
            $compressedFile = $file . '.gz';
            exec("gzip -c \"$filePath\" > \"" . storage_path('app/backups/' . $compressedFile) . "\"");
            
            // Remove original file
            unlink($filePath);
            
            $compressedFiles[] = $compressedFile;
        }
        
        return $compressedFiles;
    }

    /**
     * Upload backups to cloud storage
     */
    protected function uploadToCloud(array $backupFiles): void
    {
        $this->info('Uploading to cloud storage...');
        
        $cloudDisk = Setting::get('backup_cloud_disk', 's3');
        
        if (!config("filesystems.disks.{$cloudDisk}")) {
            $this->warn('Cloud storage not configured. Skipping upload.');
            return;
        }
        
        foreach ($backupFiles as $file) {
            try {
                $localPath = 'backups/' . $file;
                $cloudPath = 'pos-backups/' . Carbon::now()->format('Y/m/d') . '/' . $file;
                
                Storage::disk($cloudDisk)->put($cloudPath, Storage::disk('local')->get($localPath));
                
                $this->info("Uploaded: {$file} to {$cloudPath}");
            } catch (\Exception $e) {
                $this->warn("Failed to upload {$file}: " . $e->getMessage());
            }
        }
    }

    /**
     * Add directory to zip recursively
     */
    protected function addDirToZip(ZipArchive $zip, string $dir, string $zipDir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = $zipDir . '/' . substr($filePath, strlen($dir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
    }
} 