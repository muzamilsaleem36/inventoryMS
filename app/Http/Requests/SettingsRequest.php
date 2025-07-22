<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && (Auth::user()->hasRole('admin') || Auth::user()->hasRole('manager'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'business_name' => [
                'required',
                'string',
                'max:255',
                'min:2'
            ],
            'business_address' => [
                'nullable',
                'string',
                'max:500'
            ],
            'business_phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\d\s\-\+\(\)]+$/'
            ],
            'business_email' => [
                'nullable',
                'email',
                'max:255'
            ],
            'business_website' => [
                'nullable',
                'url',
                'max:255'
            ],
            'business_logo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:2048',
                'dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000'
            ],
            'currency' => [
                'required',
                'string',
                'max:3',
                'in:USD,EUR,GBP,JPY,CAD,AUD,CHF,CNY,SEK,NZD,MXN,SGD,HKD,NOK,TRY,ZAR,BRL,INR,KRW,RUB'
            ],
            'currency_symbol' => [
                'required',
                'string',
                'max:5'
            ],
            'currency_position' => [
                'required',
                'string',
                'in:before,after'
            ],
            'decimal_places' => [
                'required',
                'integer',
                'min:0',
                'max:4'
            ],
            'tax_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100'
            ],
            'tax_name' => [
                'nullable',
                'string',
                'max:50'
            ],
            'tax_number' => [
                'nullable',
                'string',
                'max:50'
            ],
            'low_stock_threshold' => [
                'required',
                'integer',
                'min:0',
                'max:1000'
            ],
            'receipt_header' => [
                'nullable',
                'string',
                'max:255'
            ],
            'receipt_footer' => [
                'nullable',
                'string',
                'max:255'
            ],
            'receipt_width' => [
                'required',
                'integer',
                'in:58,80'
            ],
            'enable_barcode' => [
                'boolean'
            ],
            'barcode_format' => [
                'required',
                'string',
                'in:CODE128,CODE39,EAN13,EAN8,UPC_A,UPC_E,QR_CODE'
            ],
            'barcode_width' => [
                'required',
                'integer',
                'min:1',
                'max:10'
            ],
            'barcode_height' => [
                'required',
                'integer',
                'min:10',
                'max:100'
            ],
            'enable_multi_store' => [
                'boolean'
            ],
            'enable_expenses' => [
                'boolean'
            ],
            'enable_activity_logs' => [
                'boolean'
            ],
            'enable_customer_loyalty' => [
                'boolean'
            ],
            'loyalty_points_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100'
            ],
            'backup_frequency' => [
                'required',
                'string',
                'in:daily,weekly,monthly,never'
            ],
            'backup_retention_days' => [
                'required',
                'integer',
                'min:1',
                'max:365'
            ],
            'timezone' => [
                'required',
                'string',
                'max:50',
                'in:UTC,America/New_York,America/Chicago,America/Denver,America/Los_Angeles,Europe/London,Europe/Paris,Europe/Berlin,Asia/Tokyo,Asia/Shanghai,Australia/Sydney,Africa/Cairo,America/Sao_Paulo'
            ],
            'date_format' => [
                'required',
                'string',
                'max:20',
                'in:Y-m-d,m/d/Y,d/m/Y,d-m-Y,M d, Y,d M Y'
            ],
            'time_format' => [
                'required',
                'string',
                'max:20',
                'in:H:i,h:i A,H:i:s,h:i:s A'
            ],
            'language' => [
                'required',
                'string',
                'max:5',
                'in:en,es,fr,de,it,pt,zh,ja,ko,ar,ru,hi'
            ],
            'email_notifications' => [
                'boolean'
            ],
            'sms_notifications' => [
                'boolean'
            ],
            'push_notifications' => [
                'boolean'
            ],
            'notification_email' => [
                'nullable',
                'email',
                'max:255'
            ],
            'notification_phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\d\s\-\+\(\)]+$/'
            ],
            'auto_logout_time' => [
                'required',
                'integer',
                'min:5',
                'max:1440'
            ],
            'session_timeout' => [
                'required',
                'integer',
                'min:10',
                'max:7200'
            ],
            'max_login_attempts' => [
                'required',
                'integer',
                'min:3',
                'max:10'
            ],
            'enable_two_factor' => [
                'boolean'
            ],
            'password_expiry_days' => [
                'nullable',
                'integer',
                'min:30',
                'max:365'
            ],
            'maintenance_mode' => [
                'boolean'
            ],
            'maintenance_message' => [
                'nullable',
                'string',
                'max:500'
            ],
            'api_rate_limit' => [
                'required',
                'integer',
                'min:10',
                'max:1000'
            ],
            'default_items_per_page' => [
                'required',
                'integer',
                'min:10',
                'max:100'
            ],
            'enable_debug_mode' => [
                'boolean'
            ],
            'remove_logo' => [
                'boolean'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'business_name.required' => 'Business name is required.',
            'business_name.min' => 'Business name must be at least 2 characters.',
            'business_name.max' => 'Business name cannot exceed 255 characters.',
            'business_phone.regex' => 'Business phone number format is invalid.',
            'business_email.email' => 'Please enter a valid business email address.',
            'business_website.url' => 'Please enter a valid website URL.',
            'business_logo.image' => 'Business logo must be an image file.',
            'business_logo.mimes' => 'Business logo must be a file of type: jpeg, png, jpg, gif, svg.',
            'business_logo.max' => 'Business logo size cannot exceed 2MB.',
            'business_logo.dimensions' => 'Business logo dimensions must be between 100x100 and 1000x1000 pixels.',
            'currency.required' => 'Currency is required.',
            'currency.in' => 'Invalid currency selected.',
            'currency_symbol.required' => 'Currency symbol is required.',
            'currency_position.required' => 'Currency position is required.',
            'currency_position.in' => 'Currency position must be either before or after.',
            'decimal_places.required' => 'Decimal places is required.',
            'decimal_places.min' => 'Decimal places cannot be less than 0.',
            'decimal_places.max' => 'Decimal places cannot exceed 4.',
            'tax_rate.min' => 'Tax rate cannot be negative.',
            'tax_rate.max' => 'Tax rate cannot exceed 100%.',
            'low_stock_threshold.required' => 'Low stock threshold is required.',
            'low_stock_threshold.min' => 'Low stock threshold cannot be negative.',
            'low_stock_threshold.max' => 'Low stock threshold cannot exceed 1000.',
            'receipt_width.required' => 'Receipt width is required.',
            'receipt_width.in' => 'Receipt width must be either 58mm or 80mm.',
            'barcode_format.required' => 'Barcode format is required.',
            'barcode_format.in' => 'Invalid barcode format selected.',
            'barcode_width.required' => 'Barcode width is required.',
            'barcode_width.min' => 'Barcode width must be at least 1.',
            'barcode_width.max' => 'Barcode width cannot exceed 10.',
            'barcode_height.required' => 'Barcode height is required.',
            'barcode_height.min' => 'Barcode height must be at least 10.',
            'barcode_height.max' => 'Barcode height cannot exceed 100.',
            'loyalty_points_rate.min' => 'Loyalty points rate cannot be negative.',
            'loyalty_points_rate.max' => 'Loyalty points rate cannot exceed 100%.',
            'backup_frequency.required' => 'Backup frequency is required.',
            'backup_frequency.in' => 'Invalid backup frequency selected.',
            'backup_retention_days.required' => 'Backup retention days is required.',
            'backup_retention_days.min' => 'Backup retention must be at least 1 day.',
            'backup_retention_days.max' => 'Backup retention cannot exceed 365 days.',
            'timezone.required' => 'Timezone is required.',
            'timezone.in' => 'Invalid timezone selected.',
            'date_format.required' => 'Date format is required.',
            'date_format.in' => 'Invalid date format selected.',
            'time_format.required' => 'Time format is required.',
            'time_format.in' => 'Invalid time format selected.',
            'language.required' => 'Language is required.',
            'language.in' => 'Invalid language selected.',
            'notification_email.email' => 'Please enter a valid notification email address.',
            'notification_phone.regex' => 'Notification phone number format is invalid.',
            'auto_logout_time.required' => 'Auto logout time is required.',
            'auto_logout_time.min' => 'Auto logout time must be at least 5 minutes.',
            'auto_logout_time.max' => 'Auto logout time cannot exceed 1440 minutes (24 hours).',
            'session_timeout.required' => 'Session timeout is required.',
            'session_timeout.min' => 'Session timeout must be at least 10 seconds.',
            'session_timeout.max' => 'Session timeout cannot exceed 7200 seconds (2 hours).',
            'max_login_attempts.required' => 'Maximum login attempts is required.',
            'max_login_attempts.min' => 'Maximum login attempts must be at least 3.',
            'max_login_attempts.max' => 'Maximum login attempts cannot exceed 10.',
            'password_expiry_days.min' => 'Password expiry must be at least 30 days.',
            'password_expiry_days.max' => 'Password expiry cannot exceed 365 days.',
            'maintenance_message.max' => 'Maintenance message cannot exceed 500 characters.',
            'api_rate_limit.required' => 'API rate limit is required.',
            'api_rate_limit.min' => 'API rate limit must be at least 10 requests per minute.',
            'api_rate_limit.max' => 'API rate limit cannot exceed 1000 requests per minute.',
            'default_items_per_page.required' => 'Default items per page is required.',
            'default_items_per_page.min' => 'Default items per page must be at least 10.',
            'default_items_per_page.max' => 'Default items per page cannot exceed 100.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'business_name' => 'business name',
            'business_address' => 'business address',
            'business_phone' => 'business phone',
            'business_email' => 'business email',
            'business_website' => 'business website',
            'business_logo' => 'business logo',
            'currency_symbol' => 'currency symbol',
            'currency_position' => 'currency position',
            'decimal_places' => 'decimal places',
            'tax_rate' => 'tax rate',
            'tax_name' => 'tax name',
            'tax_number' => 'tax number',
            'low_stock_threshold' => 'low stock threshold',
            'receipt_header' => 'receipt header',
            'receipt_footer' => 'receipt footer',
            'receipt_width' => 'receipt width',
            'enable_barcode' => 'barcode enabled',
            'barcode_format' => 'barcode format',
            'barcode_width' => 'barcode width',
            'barcode_height' => 'barcode height',
            'enable_multi_store' => 'multi-store enabled',
            'enable_expenses' => 'expenses enabled',
            'enable_activity_logs' => 'activity logs enabled',
            'enable_customer_loyalty' => 'customer loyalty enabled',
            'loyalty_points_rate' => 'loyalty points rate',
            'backup_frequency' => 'backup frequency',
            'backup_retention_days' => 'backup retention days',
            'date_format' => 'date format',
            'time_format' => 'time format',
            'email_notifications' => 'email notifications',
            'sms_notifications' => 'SMS notifications',
            'push_notifications' => 'push notifications',
            'notification_email' => 'notification email',
            'notification_phone' => 'notification phone',
            'auto_logout_time' => 'auto logout time',
            'session_timeout' => 'session timeout',
            'max_login_attempts' => 'maximum login attempts',
            'enable_two_factor' => 'two-factor authentication',
            'password_expiry_days' => 'password expiry days',
            'maintenance_mode' => 'maintenance mode',
            'maintenance_message' => 'maintenance message',
            'api_rate_limit' => 'API rate limit',
            'default_items_per_page' => 'default items per page',
            'enable_debug_mode' => 'debug mode enabled'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'enable_barcode' => $this->boolean('enable_barcode', false),
            'enable_multi_store' => $this->boolean('enable_multi_store', false),
            'enable_expenses' => $this->boolean('enable_expenses', false),
            'enable_activity_logs' => $this->boolean('enable_activity_logs', true),
            'enable_customer_loyalty' => $this->boolean('enable_customer_loyalty', false),
            'email_notifications' => $this->boolean('email_notifications', true),
            'sms_notifications' => $this->boolean('sms_notifications', false),
            'push_notifications' => $this->boolean('push_notifications', true),
            'enable_two_factor' => $this->boolean('enable_two_factor', false),
            'maintenance_mode' => $this->boolean('maintenance_mode', false),
            'enable_debug_mode' => $this->boolean('enable_debug_mode', false),
            'remove_logo' => $this->boolean('remove_logo', false)
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateBusinessSettings($validator);
            $this->validateSecuritySettings($validator);
        });
    }

    /**
     * Validate business-related settings.
     */
    protected function validateBusinessSettings($validator): void
    {
        // Validate loyalty points rate only if loyalty is enabled
        if ($this->enable_customer_loyalty && !$this->loyalty_points_rate) {
            $validator->errors()->add(
                'loyalty_points_rate',
                'Loyalty points rate is required when customer loyalty is enabled.'
            );
        }

        // Validate notification settings
        if ($this->email_notifications && !$this->notification_email) {
            $validator->errors()->add(
                'notification_email',
                'Notification email is required when email notifications are enabled.'
            );
        }

        if ($this->sms_notifications && !$this->notification_phone) {
            $validator->errors()->add(
                'notification_phone',
                'Notification phone is required when SMS notifications are enabled.'
            );
        }
    }

    /**
     * Validate security-related settings.
     */
    protected function validateSecuritySettings($validator): void
    {
        // Validate maintenance mode
        if ($this->maintenance_mode && !$this->maintenance_message) {
            $validator->errors()->add(
                'maintenance_message',
                'Maintenance message is required when maintenance mode is enabled.'
            );
        }

        // Validate session timeout vs auto logout
        if ($this->session_timeout && $this->auto_logout_time) {
            $autoLogoutSeconds = $this->auto_logout_time * 60;
            if ($this->session_timeout > $autoLogoutSeconds) {
                $validator->errors()->add(
                    'session_timeout',
                    'Session timeout cannot be longer than auto logout time.'
                );
            }
        }
    }
} 