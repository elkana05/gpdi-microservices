<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();
            $request->merge([
                'auth_user' => [
                    'id'       => $payload->get('sub'),
                    'name'     => $payload->get('name') ?? 'User',
                    'role'     => $payload->get('role') ?? 'public',
                    'id_rayon' => $payload->get('id_rayon') // PERBAIKAN: Menangkap id_rayon dari Token
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated or Invalid Token'], 401);
        }
        return $next($request);
    }
}