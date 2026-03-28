<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use App\Services\AuthService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthenticateGateway
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function handle(Request $request, Closure $next): Response|JsonResponse
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json(
                    ApiResponse::unauthorized(),
                    401
                );
            }

            $authUser = $this->authService->validateToken($token);

            if (!$authUser || empty($authUser['id']) || empty($authUser['role'])) {
                return response()->json(
                    ApiResponse::unauthorized(),
                    401
                );
            }

            $request->attributes->set('auth_user', $authUser);

            return $next($request);
        } catch (Throwable $e) {
            report($e);

            return response()->json(
                ApiResponse::serverError(),
                500
            );
        }
    }
}