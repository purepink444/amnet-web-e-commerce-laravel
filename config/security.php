<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains security-related configurations for the application.
    | All security settings should be reviewed and configured appropriately
    | for production environments.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy (CSP)
    |--------------------------------------------------------------------------
    */
    'csp' => [
        'enabled' => env('CSP_ENABLED', true),
        'default-src' => "'self'",
        'script-src' => "'self' 'unsafe-inline' https://cdn.jsdelivr.net https://code.jquery.com https://cdnjs.cloudflare.com",
        'style-src' => "'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com https://cdnjs.cloudflare.com",
        'font-src' => "'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com",
        'img-src' => "'self' data: https: blob:",
        'connect-src' => "'self' https://api.example.com",
        'frame-ancestors' => "'none'",
        'object-src' => "'none'",
        'base-uri' => "'self'",
        'form-action' => "'self'",
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers 
    |--------------------------------------------------------------------------
    */
    'headers' => [
        'hsts' => [
            'enabled' => env('HSTS_ENABLED', true),
            'max_age' => 31536000, // 1 year
            'include_subdomains' => true,
            'preload' => false,
        ],
        'x_frame_options' => 'DENY',
        'x_content_type_options' => 'nosniff',
        'referrer_policy' => 'strict-origin-when-cross-origin',
        'permissions_policy' => 'geolocation=(), microphone=(), camera=()',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limiting' => [
        'api' => [
            'max_attempts' => env('API_RATE_LIMIT', 60),
            'decay_minutes' => 1,
        ],
        'login' => [
            'max_attempts' => env('LOGIN_RATE_LIMIT', 5),
            'decay_minutes' => 15,
        ],
        'password_reset' => [
            'max_attempts' => env('PASSWORD_RESET_RATE_LIMIT', 3),
            'decay_minutes' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Input Validation
    |--------------------------------------------------------------------------
    */
    'validation' => [
        'max_string_length' => 255,
        'max_text_length' => 10000,
        'max_file_size' => 10240, // KB
        'allowed_file_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'],
        'sanitization' => [
            'strip_tags' => true,
            'html_entities' => true,
            'trim_whitespace' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Security
    |--------------------------------------------------------------------------
    */
    'auth' => [
        'session_lifetime' => env('SESSION_LIFETIME', 120), // minutes
        'token_lifetime' => env('SANCTUM_TOKEN_LIFETIME', 525600), // minutes (1 year)
        'password_history' => env('PASSWORD_HISTORY', 5), // remember last N passwords
        'require_strong_passwords' => env('REQUIRE_STRONG_PASSWORDS', true),
        'max_login_attempts' => env('MAX_LOGIN_ATTEMPTS', 5),
        'lockout_duration' => env('LOCKOUT_DURATION', 900), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Encryption
    |--------------------------------------------------------------------------
    */
    'encryption' => [
        'cipher' => 'AES-256-GCB', // Use authenticated encryption
        'key_rotation' => env('KEY_ROTATION_ENABLED', false),
        'backup_keys' => env('BACKUP_KEYS_ENABLED', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging and Monitoring
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'security_events' => [
            'login_attempts' => true,
            'failed_logins' => true,
            'password_changes' => true,
            'suspicious_activity' => true,
            'api_abuse' => true,
        ],
        'log_retention' => 90, // days
        'alert_thresholds' => [
            'failed_logins_per_hour' => 10,
            'api_rate_limit_hits' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Security
    |--------------------------------------------------------------------------
    */
    'database' => [
        'query_logging' => env('DB_QUERY_LOGGING', false),
        'slow_query_threshold' => 1000, // milliseconds
        'parameter_binding_required' => true,
        'prevent_sql_injection' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Security
    |--------------------------------------------------------------------------
    */
    'uploads' => [
        'scan_for_malware' => env('MALWARE_SCANNING_ENABLED', false),
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx'],
        'max_file_size' => 5120, // KB
        'image_processing' => [
            'strip_metadata' => true,
            'resize_large_images' => true,
            'max_width' => 2048,
            'max_height' => 2048,
        ],
    ],
];