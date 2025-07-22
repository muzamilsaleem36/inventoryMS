<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserActivityLog;
use Carbon\Carbon;

class SendLowStockAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:send-low-stock-alerts 
                            {--threshold= : Custom low stock threshold}
                            {--email= : Send to specific email}
                            {--force : Force send even if disabled}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send low stock alerts to administrators';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for low stock items...');
        
        try {
            // Check if low stock alerts are enabled
            if (!$this->option('force') && !Setting::get('low_stock_alert', true)) {
                $this->info('Low stock alerts are disabled.');
                return Command::SUCCESS;
            }
            
            // Get threshold
            $threshold = $this->option('threshold') ?? Setting::get('low_stock_threshold', 10);
            
            // Get low stock products
            $lowStockProducts = Product::with(['category', 'store'])
                ->where('is_active', true)
                ->where('track_stock', true)
                ->whereColumn('stock_quantity', '<=', 'min_stock_level')
                ->orWhere('stock_quantity', '<=', $threshold)
                ->get();
            
            if ($lowStockProducts->isEmpty()) {
                $this->info('No low stock items found.');
                return Command::SUCCESS;
            }
            
            $this->info('Found ' . $lowStockProducts->count() . ' low stock items.');
            
            // Get recipients
            $recipients = $this->getRecipients();
            
            if (empty($recipients)) {
                $this->warn('No recipients configured for low stock alerts.');
                return Command::SUCCESS;
            }
            
            // Send alerts
            foreach ($recipients as $recipient) {
                $this->sendLowStockAlert($recipient, $lowStockProducts);
            }
            
            $this->info('Low stock alerts sent successfully.');
            
            // Log the activity
            UserActivityLog::create([
                'user_id' => 1, // System user
                'action' => 'low_stock_alert',
                'description' => 'Low stock alerts sent for ' . $lowStockProducts->count() . ' products',
                'ip_address' => '127.0.0.1'
            ]);
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('Failed to send low stock alerts: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    /**
     * Get email recipients for alerts
     */
    protected function getRecipients(): array
    {
        $recipients = [];
        
        // Check for custom email option
        if ($this->option('email')) {
            $recipients[] = $this->option('email');
        } else {
            // Get admin users
            $adminUsers = User::role('admin')
                ->where('is_active', true)
                ->whereNotNull('email')
                ->pluck('email')
                ->toArray();
            
            $recipients = array_merge($recipients, $adminUsers);
            
            // Add notification email from settings
            $notificationEmail = Setting::get('notification_email');
            if ($notificationEmail) {
                $recipients[] = $notificationEmail;
            }
        }
        
        return array_unique($recipients);
    }
    
    /**
     * Send low stock alert email
     */
    protected function sendLowStockAlert(string $email, $products): void
    {
        $businessName = Setting::get('business_name', 'POS System');
        $subject = $businessName . ' - Low Stock Alert';
        
        // Group products by category
        $productsByCategory = $products->groupBy('category.name');
        
        // Create email content
        $emailContent = view('emails.low-stock-alert', [
            'products' => $products,
            'productsByCategory' => $productsByCategory,
            'businessName' => $businessName,
            'totalCount' => $products->count()
        ])->render();
        
        // Send email
        Mail::html($emailContent, function ($message) use ($email, $subject) {
            $message->to($email)->subject($subject);
        });
        
        $this->info('Alert sent to: ' . $email);
    }
} 