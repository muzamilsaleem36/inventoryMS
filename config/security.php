<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains security configurations for the POS system.
    |
    */

    'headers' => [
        'csp' => [
            'enabled' => env('SECURITY_CSP_ENABLED', true),
            'report_only' => env('SECURITY_CSP_REPORT_ONLY', false),
            'policies' => [
                'default-src' => "'self'",
                'script-src' => "'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
                'style-src' => "'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com",
                'font-src' => "'self' https://fonts.gstatic.com https://cdn.jsdelivr.net",
                'img-src' => "'self' data: https:",
                'connect-src' => "'self'",
                'frame-ancestors' => "'none'",
                'base-uri' => "'self'",
                'form-action' => "'self'"
            ]
        ],
        'hsts' => [
            'enabled' => env('SECURITY_HSTS_ENABLED', true),
            'max_age' => env('SECURITY_HSTS_MAX_AGE', 31536000),
            'include_subdomains' => env('SECURITY_HSTS_INCLUDE_SUBDOMAINS', true),
            'preload' => env('SECURITY_HSTS_PRELOAD', true),
        ],
        'frame_options' => env('SECURITY_FRAME_OPTIONS', 'DENY'),
        'content_type_options' => env('SECURITY_CONTENT_TYPE_OPTIONS', 'nosniff'),
        'xss_protection' => env('SECURITY_XSS_PROTECTION', '1; mode=block'),
        'referrer_policy' => env('SECURITY_REFERRER_POLICY', 'strict-origin-when-cross-origin'),
        'permissions_policy' => env('SECURITY_PERMISSIONS_POLICY', 'camera=(), microphone=(), geolocation=()'),
    ],

    'rate_limiting' => [
        'login' => [
            'max_attempts' => env('SECURITY_LOGIN_MAX_ATTEMPTS', 5),
            'decay_minutes' => env('SECURITY_LOGIN_DECAY_MINUTES', 15),
        ],
        'api' => [
            'max_attempts' => env('SECURITY_API_MAX_ATTEMPTS', 60),
            'decay_minutes' => env('SECURITY_API_DECAY_MINUTES', 1),
        ],
        'backup' => [
            'max_attempts' => env('SECURITY_BACKUP_MAX_ATTEMPTS', 5),
            'decay_minutes' => env('SECURITY_BACKUP_DECAY_MINUTES', 60),
        ],
    ],

    'session' => [
        'timeout' => env('SECURITY_SESSION_TIMEOUT', 120), // minutes
        'concurrent_sessions' => env('SECURITY_CONCURRENT_SESSIONS', 3),
        'secure_only' => env('SECURITY_SESSION_SECURE_ONLY', false),
        'http_only' => env('SECURITY_SESSION_HTTP_ONLY', true),
        'same_site' => env('SECURITY_SESSION_SAME_SITE', 'lax'),
    ],

    'password' => [
        'min_length' => env('SECURITY_PASSWORD_MIN_LENGTH', 8),
        'require_uppercase' => env('SECURITY_PASSWORD_REQUIRE_UPPERCASE', true),
        'require_lowercase' => env('SECURITY_PASSWORD_REQUIRE_LOWERCASE', true),
        'require_numbers' => env('SECURITY_PASSWORD_REQUIRE_NUMBERS', true),
        'require_symbols' => env('SECURITY_PASSWORD_REQUIRE_SYMBOLS', true),
        'max_age_days' => env('SECURITY_PASSWORD_MAX_AGE_DAYS', 90),
        'history_count' => env('SECURITY_PASSWORD_HISTORY_COUNT', 5),
    ],

    'two_factor' => [
        'enabled' => env('SECURITY_2FA_ENABLED', false),
        'required_for_admin' => env('SECURITY_2FA_REQUIRED_FOR_ADMIN', false),
        'backup_codes_count' => env('SECURITY_2FA_BACKUP_CODES_COUNT', 8),
        'window' => env('SECURITY_2FA_WINDOW', 30),
    ],

    'file_upload' => [
        'max_size' => env('SECURITY_FILE_MAX_SIZE', 2048), // KB
        'allowed_extensions' => [
            'images' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'documents' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
            'backup' => ['sql', 'zip', 'gz', 'xml', 'json'],
        ],
        'scan_viruses' => env('SECURITY_FILE_SCAN_VIRUSES', false),
        'quarantine_path' => env('SECURITY_FILE_QUARANTINE_PATH', storage_path('app/quarantine')),
    ],

    'logging' => [
        'log_failed_logins' => env('SECURITY_LOG_FAILED_LOGINS', true),
        'log_successful_logins' => env('SECURITY_LOG_SUCCESSFUL_LOGINS', true),
        'log_privilege_escalation' => env('SECURITY_LOG_PRIVILEGE_ESCALATION', true),
        'log_sensitive_actions' => env('SECURITY_LOG_SENSITIVE_ACTIONS', true),
        'log_data_changes' => env('SECURITY_LOG_DATA_CHANGES', true),
        'retention_days' => env('SECURITY_LOG_RETENTION_DAYS', 90),
    ],

    'encryption' => [
        'sensitive_fields' => [
            'users' => ['phone', 'address'],
            'customers' => ['phone', 'address', 'tax_number'],
            'suppliers' => ['phone', 'address', 'tax_number'],
        ],
        'key_rotation_days' => env('SECURITY_KEY_ROTATION_DAYS', 90),
    ],

    'ip_whitelist' => [
        'enabled' => env('SECURITY_IP_WHITELIST_ENABLED', false),
        'admin_only' => env('SECURITY_IP_WHITELIST_ADMIN_ONLY', false),
        'allowed_ips' => explode(',', env('SECURITY_IP_WHITELIST_ALLOWED_IPS', '')),
    ],

    'maintenance' => [
        'allowed_ips' => explode(',', env('SECURITY_MAINTENANCE_ALLOWED_IPS', '127.0.0.1')),
        'bypass_key' => env('SECURITY_MAINTENANCE_BYPASS_KEY', null),
    ],

    'backup' => [
        'encryption_enabled' => env('SECURITY_BACKUP_ENCRYPTION_ENABLED', true),
        'encryption_key' => env('SECURITY_BACKUP_ENCRYPTION_KEY', env('APP_KEY')),
        'require_authentication' => env('SECURITY_BACKUP_REQUIRE_AUTH', true),
        'audit_downloads' => env('SECURITY_BACKUP_AUDIT_DOWNLOADS', true),
    ],

    'api' => [
        'require_https' => env('SECURITY_API_REQUIRE_HTTPS', true),
        'token_expiry_minutes' => env('SECURITY_API_TOKEN_EXPIRY_MINUTES', 60),
        'signature_required' => env('SECURITY_API_SIGNATURE_REQUIRED', false),
        'signature_algorithm' => env('SECURITY_API_SIGNATURE_ALGORITHM', 'sha256'),
    ],

    'monitoring' => [
        'enabled' => env('SECURITY_MONITORING_ENABLED', true),
        'suspicious_activity_threshold' => env('SECURITY_MONITORING_SUSPICIOUS_THRESHOLD', 5),
        'alert_email' => env('SECURITY_MONITORING_ALERT_EMAIL', null),
        'alert_webhook' => env('SECURITY_MONITORING_ALERT_WEBHOOK', null),
    ],

    'data_retention' => [
        'user_activity_logs' => env('SECURITY_DATA_RETENTION_ACTIVITY_LOGS', 365), // days
        'failed_login_logs' => env('SECURITY_DATA_RETENTION_FAILED_LOGINS', 90), // days
        'backup_files' => env('SECURITY_DATA_RETENTION_BACKUP_FILES', 30), // days
        'session_data' => env('SECURITY_DATA_RETENTION_SESSION_DATA', 7), // days
    ],

    'compliance' => [
        'gdpr_enabled' => env('SECURITY_GDPR_ENABLED', false),
        'data_export_enabled' => env('SECURITY_DATA_EXPORT_ENABLED', true),
        'data_deletion_enabled' => env('SECURITY_DATA_DELETION_ENABLED', true),
        'audit_trail_required' => env('SECURITY_AUDIT_TRAIL_REQUIRED', true),
        'consent_required' => env('SECURITY_CONSENT_REQUIRED', false),
    ],

    'features' => [
        'disable_debug_in_production' => env('SECURITY_DISABLE_DEBUG_IN_PRODUCTION', true),
        'hide_server_signature' => env('SECURITY_HIDE_SERVER_SIGNATURE', true),
        'disable_php_info' => env('SECURITY_DISABLE_PHP_INFO', true),
        'secure_cookie_flags' => env('SECURITY_SECURE_COOKIE_FLAGS', true),
        'sql_injection_protection' => env('SECURITY_SQL_INJECTION_PROTECTION', true),
        'xss_protection' => env('SECURITY_XSS_PROTECTION_ENABLED', true),
    ],
]; 