<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Create a success response
     */
    public static function success(
        mixed $data = null,
        string $message = '',
        int $status = 200,
        array $meta = []
    ): JsonResponse {
        $response = [
            'success' => true,
            'timestamp' => now()->toISOString(),
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if (!empty($message)) {
            $response['message'] = $message;
        }

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $status);
    }

    /**
     * Create an error response
     */
    public static function error(
        string $message,
        int $status = 400,
        ?array $errors = null,
        ?string $errorCode = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toISOString(),
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        if ($errorCode !== null) {
            $response['error_code'] = $errorCode;
        }

        return response()->json($response, $status);
    }

    /**
     * Create a paginated success response
     */
    public static function paginated(
        mixed $data,
        string $message = '',
        int $status = 200
    ): JsonResponse {
        $paginationData = $data->toArray();

        return self::success(
            $paginationData['data'],
            $message,
            $status,
            [
                'current_page' => $paginationData['current_page'],
                'per_page' => $paginationData['per_page'],
                'total' => $paginationData['total'],
                'last_page' => $paginationData['last_page'],
                'from' => $paginationData['from'],
                'to' => $paginationData['to'],
            ]
        );
    }

    /**
     * Create a validation error response
     */
    public static function validationError(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return self::error($message, 422, $errors, 'VALIDATION_ERROR');
    }

    /**
     * Create an unauthorized response
     */
    public static function unauthorized(string $message = 'Authentication required'): JsonResponse
    {
        return self::error($message, 401, null, 'UNAUTHORIZED');
    }

    /**
     * Create a forbidden response
     */
    public static function forbidden(string $message = 'Access denied'): JsonResponse
    {
        return self::error($message, 403, null, 'FORBIDDEN');
    }

    /**
     * Create a not found response
     */
    public static function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return self::error($message, 404, null, 'NOT_FOUND');
    }

    /**
     * Create a conflict response
     */
    public static function conflict(string $message = 'Resource conflict'): JsonResponse
    {
        return self::error($message, 409, null, 'CONFLICT');
    }

    /**
     * Create a rate limited response
     */
    public static function rateLimited(string $message = 'Too many requests'): JsonResponse
    {
        return self::error($message, 429, null, 'RATE_LIMITED');
    }

    /**
     * Create an internal server error response
     */
    public static function internalError(string $message = 'Internal server error'): JsonResponse
    {
        return self::error($message, 500, null, 'INTERNAL_ERROR');
    }
}