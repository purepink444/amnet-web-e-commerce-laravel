<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for API responses, error codes, and other API-related
    | settings.
    |
    */

    'error_codes' => [
        'VALIDATION_ERROR' => 'Request validation failed',
        'UNAUTHORIZED' => 'Authentication required',
        'FORBIDDEN' => 'Access denied',
        'NOT_FOUND' => 'Resource not found',
        'CONFLICT' => 'Resource conflict',
        'RATE_LIMITED' => 'Too many requests',
        'INTERNAL_ERROR' => 'Internal server error',
        'INSUFFICIENT_STOCK' => 'Insufficient stock for the requested quantity',
        'PRODUCT_NOT_ACTIVE' => 'Product is not available',
        'INVALID_QUANTITY' => 'Invalid quantity specified',
        'CART_EMPTY' => 'Shopping cart is empty',
        'PAYMENT_FAILED' => 'Payment processing failed',
        'ORDER_NOT_FOUND' => 'Order not found',
        'ORDER_CANNOT_CANCEL' => 'Order cannot be cancelled at this stage',
    ],

    'pagination' => [
        'default_per_page' => 15,
        'max_per_page' => 100,
    ],

    'rate_limits' => [
        'api' => 60, // requests per minute
        'auth' => 10, // requests per minute for auth endpoints
    ],

    'response_format' => [
        'include_timestamp' => true,
        'include_request_id' => false,
        'pretty_print' => env('APP_DEBUG', false),
    ],

    'cors' => [
        'allowed_origins' => ['*'],
        'allowed_headers' => ['*'],
        'allowed_methods' => ['*'],
        'max_age' => 86400,
    ],
];