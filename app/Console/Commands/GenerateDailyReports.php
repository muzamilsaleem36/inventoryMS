<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserActivityLog;
use Carbon\Carbon;

class GenerateDailyReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:generate-daily-reports 
                            {--date= : Specific date (Y-m-d format)}
                            {--email= : Send to specific email}
                            {--save : Save report to file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and send daily business reports';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating daily reports...');
        
        try {
            // Get date
            $date = $this->option('date') ? 
                Carbon::parse($this->option('date')) : 
                Carbon::yesterday();
            
            // Generate report data
            $reportData = $this->generateReportData($date);
            
            // Save report if requested
            if ($this->option('save')) {
                $this->saveReport($reportData, $date);
            }
            
            // Send report if email notifications are enabled
            if (Setting::get('mail_daily_reports_enabled', false) || $this->option('email')) {
                $this->sendDailyReport($reportData, $date);
            }
            
            $this->info('Daily reports generated successfully for ' . $date->format('Y-m-d'));
            
            // Log the activity
            UserActivityLog::create([
                'user_id' => 1, // System user
                'action' => 'daily_report_generated',
                'description' => 'Daily report generated for ' . $date->format('Y-m-d'),
                'ip_address' => '127.0.0.1'
            ]);
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('Failed to generate daily reports: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    /**
     * Generate report data for the specified date
     */
    protected function generateReportData(Carbon $date): array
    {
        $startDate = $date->copy()->startOfDay();
        $endDate = $date->copy()->endOfDay();
        
        // Sales data
        $sales = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();
        
        $totalSales = $sales->sum('total_amount');
        $totalOrders = $sales->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        
        // Payment method breakdown
        $paymentMethods = $sales->groupBy('payment_method')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('total_amount')
                ];
            });
        
        // Top selling products
        $topProducts = Product::whereHas('saleItems.sale', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
                  ->where('status', 'completed');
        })
        ->withSum(['saleItems' => function ($query) use ($startDate, $endDate) {
            $query->whereHas('sale', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('status', 'completed');
            });
        }], 'quantity')
        ->orderBy('sale_items_sum_quantity', 'desc')
        ->limit(10)
        ->get();
        
        // Purchases data
        $purchases = Purchase::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();
        
        $totalPurchases = $purchases->sum('total_amount');
        
        // Expenses data
        $expenses = Expense::whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();
        
        $totalExpenses = $expenses->sum('amount');
        
        // Customer data
        $newCustomers = Customer::whereBetween('created_at', [$startDate, $endDate])->count();
        
        // Low stock items
        $lowStockItems = Product::where('is_active', true)
            ->where('track_stock', true)
            ->whereColumn('stock_quantity', '<=', 'min_stock_level')
            ->count();
        
        return [
            'date' => $date,
            'sales' => [
                'total' => $totalSales,
                'count' => $totalOrders,
                'average' => $averageOrderValue,
                'payment_methods' => $paymentMethods
            ],
            'purchases' => [
                'total' => $totalPurchases,
                'count' => $purchases->count()
            ],
            'expenses' => [
                'total' => $totalExpenses,
                'count' => $expenses->count()
            ],
            'customers' => [
                'new' => $newCustomers
            ],
            'inventory' => [
                'low_stock_items' => $lowStockItems
            ],
            'top_products' => $topProducts,
            'net_profit' => $totalSales - $totalPurchases - $totalExpenses
        ];
    }
    
    /**
     * Save report to file
     */
    protected function saveReport(array $data, Carbon $date): void
    {
        $filename = 'daily_report_' . $date->format('Y-m-d') . '.json';
        
        if (!file_exists(storage_path('app/reports'))) {
            mkdir(storage_path('app/reports'), 0755, true);
        }
        
        file_put_contents(
            storage_path('app/reports/' . $filename),
            json_encode($data, JSON_PRETTY_PRINT)
        );
        
        $this->info('Report saved to: ' . $filename);
    }
    
    /**
     * Send daily report via email
     */
    protected function sendDailyReport(array $data, Carbon $date): void
    {
        $recipients = $this->getRecipients();
        
        if (empty($recipients)) {
            $this->warn('No recipients configured for daily reports.');
            return;
        }
        
        $businessName = Setting::get('business_name', 'POS System');
        $subject = $businessName . ' - Daily Report - ' . $date->format('M d, Y');
        
        foreach ($recipients as $recipient) {
            // Create email content
            $emailContent = view('emails.daily-report', [
                'data' => $data,
                'businessName' => $businessName,
                'date' => $date
            ])->render();
            
            // Send email
            Mail::html($emailContent, function ($message) use ($recipient, $subject) {
                $message->to($recipient)->subject($subject);
            });
            
            $this->info('Daily report sent to: ' . $recipient);
        }
    }
    
    /**
     * Get email recipients for daily reports
     */
    protected function getRecipients(): array
    {
        $recipients = [];
        
        // Check for custom email option
        if ($this->option('email')) {
            $recipients[] = $this->option('email');
        } else {
            // Get admin and manager users
            $users = User::whereIn('name', ['admin', 'manager'])
                ->where('is_active', true)
                ->whereNotNull('email')
                ->pluck('email')
                ->toArray();
            
            $recipients = array_merge($recipients, $users);
            
            // Add notification email from settings
            $notificationEmail = Setting::get('notification_email');
            if ($notificationEmail) {
                $recipients[] = $notificationEmail;
            }
        }
        
        return array_unique($recipients);
    }
} 