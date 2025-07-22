<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|manager');
    }

    public function index()
    {
        $settings = Setting::pluck('value', 'key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_address' => 'nullable|string|max:500',
            'business_phone' => 'nullable|string|max:20',
            'business_email' => 'nullable|email|max:255',
            'business_website' => 'nullable|url|max:255',
            'business_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'currency' => 'required|string|max:3',
            'currency_symbol' => 'required|string|max:5',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'low_stock_threshold' => 'required|integer|min:0',
            'receipt_header' => 'nullable|string|max:255',
            'receipt_footer' => 'nullable|string|max:255',
            'enable_barcode' => 'boolean',
            'barcode_format' => 'required|string|in:CODE128,CODE39,EAN13,EAN8,UPC_A,UPC_E',
            'enable_multi_store' => 'boolean',
            'enable_expenses' => 'boolean',
            'enable_activity_logs' => 'boolean',
            'backup_frequency' => 'required|string|in:daily,weekly,monthly',
            'timezone' => 'required|string|max:50',
            'date_format' => 'required|string|max:20',
            'time_format' => 'required|string|max:20',
            'language' => 'required|string|max:5',
            'theme' => 'required|string|in:light,dark,auto',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'notification_low_stock' => 'boolean',
            'notification_new_order' => 'boolean'
        ]);

        $settingsData = $request->except(['_token', 'business_logo']);

        // Handle logo upload
        if ($request->hasFile('business_logo')) {
            $logoPath = $request->file('business_logo')->store('logos', 'public');
            $settingsData['business_logo'] = $logoPath;
        }

        // Update or create settings
        foreach ($settingsData as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'settings_updated',
            'description' => 'Updated business settings',
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully!');
    }

    public function getLogo()
    {
        $logoPath = Setting::where('key', 'business_logo')->value('value');
        
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            return response()->file(storage_path('app/public/' . $logoPath));
        }
        
        return response()->json(['error' => 'Logo not found'], 404);
    }

    public function deleteLogo()
    {
        $logoPath = Setting::where('key', 'business_logo')->value('value');
        
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            Storage::disk('public')->delete($logoPath);
            Setting::where('key', 'business_logo')->delete();
            
            // Log activity
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'logo_deleted',
                'description' => 'Deleted business logo',
                'ip_address' => request()->ip()
            ]);
        }
        
        return redirect()->route('settings.index')
            ->with('success', 'Logo deleted successfully!');
    }

    public function export()
    {
        $settings = Setting::all();
        
        $data = [
            'exported_at' => now(),
            'settings' => $settings->toArray()
        ];
        
        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'settings_exported',
            'description' => 'Exported settings configuration',
            'ip_address' => request()->ip()
        ]);
        
        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="settings-export-' . date('Y-m-d') . '.json"');
    }

    public function import(Request $request)
    {
        $request->validate([
            'settings_file' => 'required|file|mimes:json'
        ]);

        $file = $request->file('settings_file');
        $content = json_decode(file_get_contents($file->path()), true);

        if (!$content || !isset($content['settings'])) {
            return redirect()->back()->with('error', 'Invalid settings file format.');
        }

        try {
            foreach ($content['settings'] as $setting) {
                if (isset($setting['key']) && isset($setting['value'])) {
                    Setting::updateOrCreate(
                        ['key' => $setting['key']],
                        ['value' => $setting['value']]
                    );
                }
            }

            // Log activity
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'settings_imported',
                'description' => 'Imported settings configuration',
                'ip_address' => request()->ip()
            ]);

            return redirect()->route('settings.index')
                ->with('success', 'Settings imported successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing settings: ' . $e->getMessage());
        }
    }

    public function reset()
    {
        // Get default settings
        $defaultSettings = $this->getDefaultSettings();
        
        // Reset all settings to defaults
        foreach ($defaultSettings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Log activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'settings_reset',
            'description' => 'Reset all settings to default values',
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Settings reset to default values!');
    }

    private function getDefaultSettings()
    {
        return [
            'business_name' => 'My Business',
            'business_address' => '',
            'business_phone' => '',
            'business_email' => '',
            'business_website' => '',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'tax_rate' => 0,
            'low_stock_threshold' => 10,
            'receipt_header' => 'Thank you for your business!',
            'receipt_footer' => 'Visit us again soon!',
            'enable_barcode' => true,
            'barcode_format' => 'CODE128',
            'enable_multi_store' => false,
            'enable_expenses' => false,
            'enable_activity_logs' => true,
            'backup_frequency' => 'weekly',
            'timezone' => 'UTC',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            'language' => 'en',
            'theme' => 'light',
            'email_notifications' => true,
            'sms_notifications' => false,
            'notification_low_stock' => true,
            'notification_new_order' => false
        ];
    }
} 