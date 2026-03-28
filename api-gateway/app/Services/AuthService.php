<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

class AuthService
{
    public function validateToken(string $token): ?array
    {
        $baseUrl = config('services.user_service.base_url');
        $timeout = config('services.gateway.timeout', 10);
        $connectTimeout = config('services.gateway.connect_timeout', 3);

        try {
            $response = Http::acceptJson()
                ->timeout($timeout)
                ->connectTimeout($connectTimeout)
                ->withToken($token)
                ->get("{$baseUrl}/api/auth/me");

            if (!$response->successful()) {
                return null;
            }

            $payload = $response->json();

            if (!is_array($payload)) {
                return null;
            }

            $user = data_get($payload, 'data.user');

            if (!$user && data_get($payload, 'data')) {
                $user = data_get($payload, 'data');
            }

            if (!is_array($user)) {
                return null;
            }

            return [
                'id' => $user['id'] ?? null,
                'name' => $user['name'] ?? null,
                'email' => $user['email'] ?? null,
                'role' => $user['role'] ?? null,
            ];
        } catch (Throwable) {
            return null;
        }
    }
}