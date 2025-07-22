<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;
use App\Models\UserActivityLog;
use Carbon\Carbon;
use ZipArchive;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Display backup management page
     */
    public function index()
    {
        $backups = $this->getBackupFiles();
        $settings = $this->getBackupSettings();
        
        return view('backups.index', compact('backups', 'settings'));
    }

    /**
     * Create manual backup
     */
    public function create(Request $request)
    {
        $request->validate([
            'format' => 'required|in:sql,xml,excel,full',
            'compress' => 'boolean',
            'cloud_upload' => 'boolean',
            'tables' => 'nullable|string'
        ]);

        try {
            // Build artisan command
            $command = 'pos:backup-database';
            $options = [
                '--format' => $request->format,
            ];

            if ($request->compress) {
                $options['--compress'] = true;
            }

            if ($request->cloud_upload) {
                $options['--cloud'] = true;
            }

            if ($request->tables) {
                $options['--tables'] = $request->tables;
            }

            // Execute backup command
            $exitCode = Artisan::call($command, $options);

            if ($exitCode === 0) {
                // Log user activity
                UserActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'manual_backup_created',
                    'description' => 'Manual backup created with format: ' . $request->format,
                    'ip_address' => $request->ip()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Backup created successfully!',
                    'output' => Artisan::output()
                ]);
            } else {
                throw new \Exception('Backup command failed');
            }
        } catch (\Exception $e) {
            Log::error('Manual backup failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download backup file
     */
    public function download($filename)
    {
        try {
            $filePath = 'backups/' . $filename;
            
            if (!Storage::disk('local')->exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup file not found'
                ], 404);
            }

            // Log download activity
            UserActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'backup_downloaded',
                'description' => 'Downloaded backup: ' . $filename,
                'ip_address' => request()->ip()
            ]);

            return Storage::disk('local')->download($filePath, $filename);
        } catch (\Exception $e) {
            Log::error('Backup download failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Download failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete backup file
     */
    public function delete($filename)
    {
        try {
            $filePath = 'backups/' . $filename;
            
            if (!Storage::disk('local')->exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup file not found'
                ], 404);
            }

            Storage::disk('local')->delete($filePath);

            // Log deletion activity
            UserActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'backup_deleted',
                'description' => 'Deleted backup: ' . $filename,
                'ip_address' => request()->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Backup deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Backup deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export data in different formats
     */
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:xml,excel,json',
            'tables' => 'nullable|array',
            'date_range' => 'nullable|string'
        ]);

        try {
            $format = $request->format;
            $tables = $request->tables ?? ['products', 'customers', 'sales', 'categories'];
            $filename = 'pos_export_' . Carbon::now()->format('Y-m-d_H-i-s');

            switch ($format) {
                case 'xml':
                    $filePath = $this->exportToXML($tables, $filename);
                    break;
                case 'excel':
                    $filePath = $this->exportToExcel($tables, $filename);
                    break;
                case 'json':
                    $filePath = $this->exportToJSON($tables, $filename);
                    break;
            }

            // Log export activity
            UserActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'data_exported',
                'description' => 'Data exported in ' . $format . ' format',
                'ip_address' => $request->ip()
            ]);

            return response()->download($filePath)->deleteFileAfterSend();
        } catch (\Exception $e) {
            Log::error('Data export failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import data from uploaded file
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xml,xlsx,csv,json|max:10240',
            'import_type' => 'required|in:replace,merge,append'
        ]);

        try {
            $file = $request->file('import_file');
            $importType = $request->import_type;
            $extension = $file->getClientOriginalExtension();
            
            // Store uploaded file temporarily
            $tempPath = $file->storeAs('temp', 'import_' . time() . '.' . $extension);
            
            $result = null;
            switch ($extension) {
                case 'xml':
                    $result = $this->importFromXML(storage_path('app/' . $tempPath), $importType);
                    break;
                case 'xlsx':
                case 'csv':
                    $result = $this->importFromExcel(storage_path('app/' . $tempPath), $importType);
                    break;
                case 'json':
                    $result = $this->importFromJSON(storage_path('app/' . $tempPath), $importType);
                    break;
            }

            // Clean up temporary file
            Storage::disk('local')->delete($tempPath);

            // Log import activity
            UserActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'data_imported',
                'description' => 'Data imported from ' . $extension . ' file (' . $importType . ')',
                'ip_address' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data imported successfully',
                'result' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Data import failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update backup settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'backup_retention_days' => 'required|integer|min:1|max:365',
            'backup_cloud_disk' => 'nullable|string',
            'backup_auto_cloud' => 'boolean',
            'backup_notification_email' => 'nullable|email',
            'backup_low_storage_threshold' => 'nullable|integer'
        ]);

        try {
            $settings = [
                'backup_retention_days' => $request->backup_retention_days,
                'backup_cloud_disk' => $request->backup_cloud_disk,
                'backup_auto_cloud' => $request->backup_auto_cloud ?? false,
                'backup_notification_email' => $request->backup_notification_email,
                'backup_low_storage_threshold' => $request->backup_low_storage_threshold ?? 1000
            ];

            foreach ($settings as $key => $value) {
                Setting::set($key, $value);
            }

            // Log settings update
            UserActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'backup_settings_updated',
                'description' => 'Backup settings updated',
                'ip_address' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Settings update failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Settings update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get backup files list
     */
    protected function getBackupFiles()
    {
        $files = Storage::disk('local')->files('backups');
        $backups = [];

        foreach ($files as $file) {
            $filename = basename($file);
            $backups[] = [
                'filename' => $filename,
                'size' => Storage::disk('local')->size($file),
                'created_at' => Carbon::createFromTimestamp(Storage::disk('local')->lastModified($file)),
                'type' => $this->getBackupType($filename),
                'download_url' => route('backups.download', $filename)
            ];
        }

        // Sort by creation date (newest first)
        usort($backups, function($a, $b) {
            return $b['created_at']->timestamp - $a['created_at']->timestamp;
        });

        return $backups;
    }

    /**
     * Get backup settings
     */
    protected function getBackupSettings()
    {
        return [
            'retention_days' => Setting::get('backup_retention_days', 30),
            'cloud_disk' => Setting::get('backup_cloud_disk', 's3'),
            'auto_cloud' => Setting::get('backup_auto_cloud', false),
            'notification_email' => Setting::get('backup_notification_email', ''),
            'low_storage_threshold' => Setting::get('backup_low_storage_threshold', 1000)
        ];
    }

    /**
     * Get backup type from filename
     */
    protected function getBackupType($filename)
    {
        if (strpos($filename, 'full_backup') !== false) {
            return 'full';
        } elseif (strpos($filename, '.xml') !== false) {
            return 'xml';
        } elseif (strpos($filename, '.xlsx') !== false) {
            return 'excel';
        } elseif (strpos($filename, '.sql') !== false) {
            return 'sql';
        } else {
            return 'unknown';
        }
    }

    /**
     * Export data to XML
     */
    protected function exportToXML($tables, $filename)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        
        $root = $dom->createElement('pos_export');
        $root->setAttribute('created_at', Carbon::now()->toISOString());
        $dom->appendChild($root);

        foreach ($tables as $table) {
            if (!DB::getSchemaBuilder()->hasTable($table)) {
                continue;
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

        $filePath = storage_path('app/exports/' . $filename . '.xml');
        
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        
        $dom->save($filePath);
        
        return $filePath;
    }

    /**
     * Export data to Excel
     */
    protected function exportToExcel($tables, $filename)
    {
        $filePath = storage_path('app/exports/' . $filename . '.csv');
        
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        
        $csvData = [];
        
        foreach ($tables as $table) {
            if (!DB::getSchemaBuilder()->hasTable($table)) {
                continue;
            }

            $csvData[] = [strtoupper($table) . ' TABLE'];
            
            $data = DB::table($table)->get();
            
            if ($data->isNotEmpty()) {
                $headers = array_keys((array) $data->first());
                $csvData[] = $headers;
                
                foreach ($data as $row) {
                    $csvData[] = array_values((array) $row);
                }
            }
            
            $csvData[] = [''];
        }
        
        $handle = fopen($filePath, 'w');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
        
        return $filePath;
    }

    /**
     * Export data to JSON
     */
    protected function exportToJSON($tables, $filename)
    {
        $exportData = [
            'created_at' => Carbon::now()->toISOString(),
            'tables' => []
        ];
        
        foreach ($tables as $table) {
            if (!DB::getSchemaBuilder()->hasTable($table)) {
                continue;
            }

            $data = DB::table($table)->get();
            $exportData['tables'][$table] = $data->toArray();
        }
        
        $filePath = storage_path('app/exports/' . $filename . '.json');
        
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        
        file_put_contents($filePath, json_encode($exportData, JSON_PRETTY_PRINT));
        
        return $filePath;
    }

    /**
     * Import data from XML
     */
    protected function importFromXML($filePath, $importType)
    {
        $dom = new \DOMDocument();
        $dom->load($filePath);
        
        $tables = $dom->getElementsByTagName('table');
        $importedCount = 0;
        
        foreach ($tables as $table) {
            $tableName = $table->getAttribute('name');
            
            if (!DB::getSchemaBuilder()->hasTable($tableName)) {
                continue;
            }

            $rows = $table->getElementsByTagName('row');
            
            foreach ($rows as $row) {
                $data = [];
                $columns = $row->getElementsByTagName('column');
                
                foreach ($columns as $column) {
                    $name = $column->getAttribute('name');
                    $value = $column->getAttribute('value');
                    $data[$name] = $value;
                }
                
                if ($importType === 'replace') {
                    DB::table($tableName)->updateOrInsert(['id' => $data['id']], $data);
                } else {
                    DB::table($tableName)->insert($data);
                }
                
                $importedCount++;
            }
        }
        
        return ['imported_records' => $importedCount];
    }

    /**
     * Import data from Excel/CSV
     */
    protected function importFromExcel($filePath, $importType)
    {
        $handle = fopen($filePath, 'r');
        $importedCount = 0;
        $currentTable = '';
        $headers = [];
        
        while (($row = fgetcsv($handle)) !== FALSE) {
            if (empty($row[0])) {
                continue;
            }
            
            if (strpos($row[0], 'TABLE') !== false) {
                $currentTable = strtolower(str_replace(' TABLE', '', $row[0]));
                $headers = [];
                continue;
            }
            
            if (empty($headers) && !empty($currentTable)) {
                $headers = $row;
                continue;
            }
            
            if (!empty($headers) && !empty($currentTable)) {
                if (DB::getSchemaBuilder()->hasTable($currentTable)) {
                    $data = array_combine($headers, $row);
                    
                    if ($importType === 'replace') {
                        DB::table($currentTable)->updateOrInsert(['id' => $data['id']], $data);
                    } else {
                        DB::table($currentTable)->insert($data);
                    }
                    
                    $importedCount++;
                }
            }
        }
        
        fclose($handle);
        
        return ['imported_records' => $importedCount];
    }

    /**
     * Import data from JSON
     */
    protected function importFromJSON($filePath, $importType)
    {
        $jsonData = json_decode(file_get_contents($filePath), true);
        $importedCount = 0;
        
        if (isset($jsonData['tables'])) {
            foreach ($jsonData['tables'] as $tableName => $tableData) {
                if (!DB::getSchemaBuilder()->hasTable($tableName)) {
                    continue;
                }
                
                foreach ($tableData as $row) {
                    $data = (array) $row;
                    
                    if ($importType === 'replace') {
                        DB::table($tableName)->updateOrInsert(['id' => $data['id']], $data);
                    } else {
                        DB::table($tableName)->insert($data);
                    }
                    
                    $importedCount++;
                }
            }
        }
        
        return ['imported_records' => $importedCount];
    }
} 