<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated functionality that is being used in your application.
    | This allows you to get your application ready for upcoming major versions.
    |
    */

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
            'replace_placeholders' => true,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'POS System',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
            'replace_placeholders' => true,
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://'.env('PAPERTRAIL_URL').':'.env('PAPERTRAIL_PORT'),
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
            'facility' => LOG_USER,
            'replace_placeholders' => true,
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        /*
        |--------------------------------------------------------------------------
        | POS System Specific Log Channels
        |--------------------------------------------------------------------------
        |
        | Custom log channels for different aspects of the POS system
        |
        */

        'pos_sales' => [
            'driver' => 'daily',
            'path' => storage_path('logs/pos_sales.log'),
            'level' => env('LOG_LEVEL', 'info'),
            'days' => 30,
            'replace_placeholders' => true,
        ],

        'pos_inventory' => [
            'driver' => 'daily',
            'path' => storage_path('logs/pos_inventory.log'),
            'level' => env('LOG_LEVEL', 'info'),
            'days' => 30,
            'replace_placeholders' => true,
        ],

        'pos_auth' => [
            'driver' => 'daily',
            'path' => storage_path('logs/pos_auth.log'),
            'level' => env('LOG_LEVEL', 'info'),
            'days' => 60,
            'replace_placeholders' => true,
        ],

        'pos_errors' => [
            'driver' => 'daily',
            'path' => storage_path('logs/pos_errors.log'),
            'level' => env('LOG_LEVEL', 'error'),
            'days' => 90,
            'replace_placeholders' => true,
        ],

        'pos_payments' => [
            'driver' => 'daily',
            'path' => storage_path('logs/pos_payments.log'),
            'level' => env('LOG_LEVEL', 'info'),
            'days' => 90,
            'replace_placeholders' => true,
        ],

        'pos_reports' => [
            'driver' => 'daily',
            'path' => storage_path('logs/pos_reports.log'),
            'level' => env('LOG_LEVEL', 'info'),
            'days' => 30,
            'replace_placeholders' => true,
        ],

        'pos_backups' => [
            'driver' => 'daily',
            'path' => storage_path('logs/pos_backups.log'),
            'level' => env('LOG_LEVEL', 'info'),
            'days' => 30,
            'replace_placeholders' => true,
        ],

        'pos_security' => [
            'driver' => 'daily',
            'path' => storage_path('logs/pos_security.log'),
            'level' => env('LOG_LEVEL', 'warning'),
            'days' => 90,
            'replace_placeholders' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | POS System Logging Settings
    |--------------------------------------------------------------------------
    |
    | Additional logging settings specific to the POS system
    |
    */

    'pos_settings' => [
        'log_user_activities' => env('POS_LOG_USER_ACTIVITIES', true),
        'log_sales_transactions' => env('POS_LOG_SALES_TRANSACTIONS', true),
        'log_inventory_changes' => env('POS_LOG_INVENTORY_CHANGES', true),
        'log_payment_attempts' => env('POS_LOG_PAYMENT_ATTEMPTS', true),
        'log_failed_logins' => env('POS_LOG_FAILED_LOGINS', true),
        'log_api_requests' => env('POS_LOG_API_REQUESTS', false),
        'log_database_queries' => env('POS_LOG_DATABASE_QUERIES', false),
    ],
]; 