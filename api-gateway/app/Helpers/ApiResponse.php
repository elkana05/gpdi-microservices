<?php

namespace App\Helpers;

class ApiResponse
{
    public static function success(
        string $message = 'Success',
        mixed $data = null,
        mixed $meta = null
    ): array {
        return [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
        ];
    }

    public static function error(
        string $message = 'Internal server error',
        array $errors = []
    ): array {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return $response;
    }

    public static function validation(
        string $message = 'Validation failed',
        array $errors = []
    ): array {
        return [
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ];
    }

    public static function unauthorized(
        string $message = 'Unauthenticated'
    ): array {
        return self::error($message);
    }

    public static function forbidden(
        string $message = 'You do not have permission to access this resource'
    ): array {
        return self::error($message);
    }

    public static function notFound(
        string $message = 'Resource not found'
    ): array {
        return self::error($message);
    }

    public static function serverError(
        string $message = 'Internal server error'
    ): array {
        return self::error($message);
    }
}