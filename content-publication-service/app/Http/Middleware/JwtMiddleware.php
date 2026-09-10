<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class JwtMiddleware
{
    /**
     * Memvalidasi token JWT tanpa melakukan query ke database local (microservice approach).
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Membaca dan memvalidasi token murni dari header
            $payload = JWTAuth::parseToken()->getPayload();

            // Menyisipkan data pengguna dari klaim JWT ke dalam request agar bisa dipakai di Controller
            $request->merge([
                'auth_user' => [
                    'id'       => $payload->get('sub'),
                    'name'     => $payload->get('name') ?? 'User',
                    'role'     => $payload->get('role') ?? 'public',
                    'id_rayon' => $payload->get('id_rayon') ?? null,
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Unauthenticated or Invalid Token'
            ], 401);
        }

        return $next($request);
    }
}