<?php

namespace App\Http\Controllers;

use App\Exceptions\GatewayException;
use App\Helpers\ApiResponse;
use App\Services\ProxyService;
use App\Services\ServiceRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class GatewayController extends Controller
{
    public function __construct(
        protected ProxyService $proxyService
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $path = ltrim($request->path(), '/');
        $targetBaseUrl = ServiceRegistry::resolve($path);

        if (!$targetBaseUrl) {
            return response()->json(
                ApiResponse::notFound(),
                404
            );
        }

        try {
            $response = $this->proxyService->forward($request, $targetBaseUrl);

            return response()->json(
                $response['body'],
                $response['status']
            );
        } catch (GatewayException $e) {
            report($e);

            return response()->json(
                ApiResponse::serverError(),
                500
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json(
                ApiResponse::serverError(),
                500
            );
        }
    }
}