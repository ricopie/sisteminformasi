<?php

declare(strict_types=1);

namespace Shared\Responses;

use Illuminate\Http\JsonResponse;

final class ApiResponse
{
    /**
     * Success response.
     *
     * @param  mixed  $data  Response data
     * @param  string|null  $message  Success message
     * @param  int  $code  HTTP status code
     * @param  array<string, mixed>  $extra  Additional fields to merge
     */
    public static function success(
        mixed $data = null,
        ?string $message = null,
        int $code = 200,
        array $extra = [],
    ): JsonResponse {
        return response()->json(array_merge([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
        ], $extra), $code);
    }

    /**
     * Error response.
     *
     * @param  string  $message  Error message
     * @param  int  $code  HTTP status code
     * @param  mixed  $errors  Validation errors or additional error details
     * @param  array<string, mixed>  $extra  Additional fields to merge
     */
    public static function error(
        string $message,
        int $code = 400,
        mixed $errors = null,
        array $extra = [],
    ): JsonResponse {
        return response()->json(array_merge([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ], $extra), $code);
    }

    /**
     * Created response (201).
     *
     * @param  mixed  $data  Created resource data
     * @param  string|null  $message  Success message
     */
    public static function created(
        mixed $data = null,
        ?string $message = 'Resource created successfully.',
    ): JsonResponse {
        return self::success($data, $message, 201);
    }

    /**
     * No content response (204).
     */
    public static function noContent(): JsonResponse
    {
        return self::success(null, null, 204);
    }
}
