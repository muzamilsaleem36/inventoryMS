<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\UserActivityLog;
use Carbon\Carbon;

class CleanupSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:cleanup-system 
                            {--logs : Clean old activity logs}
                            {--temp : Clean temporary files}
                            {--cache : Clear application cache}
                            {--sessions : Clean expired sessions}
                            {--all : Run all cleanup tasks}
                            {--days=30 : Number of days to keep logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up system files and old data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting system cleanup...');
        
        try {
            $all = $this->option('all');
            $days = $this->option('days');
            
            if ($all || $this->option('logs')) {
                $this->cleanupActivityLogs($days);
            }
            
            if ($all || $this->option('temp')) {
                $this->cleanupTemporaryFiles();
            }
            
            if ($all || $this->option('cache')) {
                $this->clearApplicationCache();
            }
            
            if ($all || $this->option('sessions')) {
                $this->cleanupExpiredSessions();
            }
            
            $this->info('System cleanup completed successfully.');
            
            // Log the cleanup activity
            UserActivityLog::create([
                'user_id' => 1, // System user
                'action' => 'system_cleanup',
                'description' => 'System cleanup completed',
                'ip_address' => '127.0.0.1'
            ]);
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('System cleanup failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    /**
     * Clean up old activity logs
     */
    protected function cleanupActivityLogs(int $days): void
    {
        $this->info('Cleaning up activity logs older than ' . $days . ' days...');
        
        $cutoffDate = Carbon::now()->subDays($days);
        $deletedCount = UserActivityLog::where('created_at', '<', $cutoffDate)->delete();
        
        $this->info('Deleted ' . $deletedCount . ' old activity log entries.');
    }
    
    /**
     * Clean up temporary files
     */
    protected function cleanupTemporaryFiles(): void
    {
        $this->info('Cleaning up temporary files...');
        
        $tempDirs = [
            'temp',
            'uploads/temp',
            'exports/temp',
            'cache/temp'
        ];
        
        $totalDeleted = 0;
        
        foreach ($tempDirs as $dir) {
            if (Storage::disk('local')->exists($dir)) {
                $files = Storage::disk('local')->files($dir);
                
                foreach ($files as $file) {
                    // Delete files older than 24 hours
                    if (Storage::disk('local')->lastModified($file) < now()->subDay()->timestamp) {
                        Storage::disk('local')->delete($file);
                        $totalDeleted++;
                    }
                }
            }
        }
        
        $this->info('Deleted ' . $totalDeleted . ' temporary files.');
    }
    
    /**
     * Clear application cache
     */
    protected function clearApplicationCache(): void
    {
        $this->info('Clearing application cache...');
        
        // Clear Laravel cache
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('view:clear');
        
        // Clear custom POS cache
        $this->clearPosCache();
        
        $this->info('Application cache cleared.');
    }
    
    /**
     * Clear POS specific cache
     */
    protected function clearPosCache(): void
    {
        $cacheTags = ['products', 'customers', 'reports', 'settings', 'dashboard'];
        
        foreach ($cacheTags as $tag) {
            cache()->tags($tag)->flush();
        }
    }
    
    /**
     * Clean up expired sessions
     */
    protected function cleanupExpiredSessions(): void
    {
        $this->info('Cleaning up expired sessions...');
        
        // Clean database sessions
        if (config('session.driver') === 'database') {
            $deletedCount = DB::table('sessions')
                ->where('last_activity', '<', now()->subMinutes(config('session.lifetime'))->timestamp)
                ->delete();
            
            $this->info('Deleted ' . $deletedCount . ' expired session records.');
        }
        
        // Clean file-based sessions
        if (config('session.driver') === 'file') {
            $sessionPath = storage_path('framework/sessions');
            
            if (is_dir($sessionPath)) {
                $files = glob($sessionPath . '/*');
                $deletedCount = 0;
                
                foreach ($files as $file) {
                    if (filemtime($file) < now()->subMinutes(config('session.lifetime'))->timestamp) {
                        unlink($file);
                        $deletedCount++;
                    }
                }
                
                $this->info('Deleted ' . $deletedCount . ' expired session files.');
            }
        }
    }
} 