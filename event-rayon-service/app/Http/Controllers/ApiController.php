<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    /**
     * Format untuk response sukses
     */
    protected function successResponse($data, string $message = 'Data retrieved successfully', int $code = 200, $meta = null): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
            'meta'    => $meta
        ], $code);
    }

    /**
     * Format untuk response error / forbidden
     */
    protected function errorResponse(string $message, int $code = 400): JsonResponse
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message
        ], $code);
    }
}