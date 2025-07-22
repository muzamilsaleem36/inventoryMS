<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'stripe' => [
        'model' => App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'paypal' => [
        'mode' => env('PAYPAL_MODE', 'sandbox'), // sandbox or live
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
    ],

    'twitter' => [
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
        'redirect' => env('TWITTER_REDIRECT_URI'),
    ],

    /*
    |--------------------------------------------------------------------------
    | POS System Services
    |--------------------------------------------------------------------------
    |
    | Third party services specific to the POS system
    |
    */

    'sms' => [
        'driver' => env('SMS_DRIVER', 'twilio'),
        'twilio' => [
            'sid' => env('TWILIO_SID'),
            'token' => env('TWILIO_TOKEN'),
            'from' => env('TWILIO_FROM'),
        ],
        'nexmo' => [
            'key' => env('NEXMO_KEY'),
            'secret' => env('NEXMO_SECRET'),
            'from' => env('NEXMO_FROM'),
        ],
    ],

    'payment_gateways' => [
        'square' => [
            'application_id' => env('SQUARE_APPLICATION_ID'),
            'access_token' => env('SQUARE_ACCESS_TOKEN'),
            'environment' => env('SQUARE_ENVIRONMENT', 'sandbox'),
        ],
        'paystack' => [
            'public_key' => env('PAYSTACK_PUBLIC_KEY'),
            'secret_key' => env('PAYSTACK_SECRET_KEY'),
        ],
        'razorpay' => [
            'key' => env('RAZORPAY_KEY'),
            'secret' => env('RAZORPAY_SECRET'),
        ],
    ],

    'barcode' => [
        'driver' => env('BARCODE_DRIVER', 'gd'),
        'format' => env('BARCODE_FORMAT', 'CODE_128'),
        'width' => env('BARCODE_WIDTH', 2),
        'height' => env('BARCODE_HEIGHT', 30),
        'font_size' => env('BARCODE_FONT_SIZE', 10),
    ],

    'backup' => [
        'driver' => env('BACKUP_DRIVER', 'local'),
        'schedule' => env('BACKUP_SCHEDULE', 'daily'),
        'retention_days' => env('BACKUP_RETENTION_DAYS', 30),
        'compress' => env('BACKUP_COMPRESS', true),
    ],

    'notification' => [
        'channels' => [
            'email' => env('NOTIFICATION_EMAIL', true),
            'sms' => env('NOTIFICATION_SMS', false),
            'push' => env('NOTIFICATION_PUSH', false),
            'slack' => env('NOTIFICATION_SLACK', false),
        ],
        'slack' => [
            'webhook_url' => env('SLACK_WEBHOOK_URL'),
            'channel' => env('SLACK_CHANNEL', '#general'),
            'username' => env('SLACK_USERNAME', 'POS System'),
        ],
    ],

    'analytics' => [
        'google_analytics' => [
            'tracking_id' => env('GOOGLE_ANALYTICS_TRACKING_ID'),
        ],
        'facebook_pixel' => [
            'pixel_id' => env('FACEBOOK_PIXEL_ID'),
        ],
    ],

    'cdn' => [
        'url' => env('CDN_URL'),
        'enabled' => env('CDN_ENABLED', false),
    ],

    'maintenance' => [
        'enabled' => env('MAINTENANCE_MODE', false),
        'message' => env('MAINTENANCE_MESSAGE', 'System is under maintenance'),
        'allowed_ips' => env('MAINTENANCE_ALLOWED_IPS', ''),
    ],
]; 