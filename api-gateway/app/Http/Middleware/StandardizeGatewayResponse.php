<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class StandardizeGatewayResponse
{
    public function handle(Request $request, Closure $next): Response|JsonResponse
    {
        try {
            $response = $next($request);

            $contentType = $response->headers->get('Content-Type');

            if ($contentType && str_contains($contentType, 'application/json')) {
                return $response;
            }

            return response()->json(
                ApiResponse::success(
                    message: 'Request processed successfully',
                    data: [
                        'content' => $response->getContent(),
                    ],
                    meta: null
                ),
                $response->getStatusCode()
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json(
                ApiResponse::error('Internal server error'),
                500
            );
        }
    }
}