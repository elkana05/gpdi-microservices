<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GatewayProxyTest extends TestCase
{
    public function test_public_route_is_proxied_to_content_service(): void
    {
        config()->set('services.content_service.base_url', 'http://localhost:8002');

        Http::fake([
            'http://localhost:8002/api/public/announcements' => Http::response([
                'status' => 'success',
                'message' => 'Announcements retrieved successfully',
                'data' => [],
                'meta' => null,
            ], 200),
        ]);

        $response = $this->getJson('/api/public/announcements');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Announcements retrieved successfully',
            ]);
    }

    public function test_private_route_without_token_returns_unauthenticated(): void
    {
        $response = $this->getJson('/api/content/announcements');

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Unauthenticated',
            ]);
    }

    public function test_auth_login_route_is_public(): void
    {
        config()->set('services.user_service.base_url', 'http://localhost:8001');

        Http::fake([
            'http://localhost:8001/api/auth/login' => Http::response([
                'status' => 'success',
                'message' => 'Login successful',
                'data' => [
                    'token' => 'dummy-token-123',
                    'user' => [
                        'id' => 1,
                        'name' => 'Pendeta Dummy',
                        'email' => 'pendeta@gpdi.local',
                        'role' => 'pendeta',
                    ],
                ],
                'meta' => null,
            ], 200),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'pendeta@gpdi.local',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Login successful',
            ]);
    }

    public function test_auth_me_with_invalid_token_returns_unauthenticated(): void
    {
        config()->set('services.user_service.base_url', 'http://localhost:8001');

        Http::fake([
            'http://localhost:8001/api/auth/me' => Http::response([
                'status' => 'error',
                'message' => 'Unauthenticated',
            ], 401),
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token',
        ])->getJson('/api/auth/me');

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Unauthenticated',
            ]);
    }

    public function test_private_route_with_valid_token_is_proxied(): void
    {
        config()->set('services.user_service.base_url', 'http://localhost:8001');
        config()->set('services.content_service.base_url', 'http://localhost:8002');

        Http::fake([
            'http://localhost:8001/api/auth/me' => Http::response([
                'status' => 'success',
                'message' => 'Authenticated user retrieved successfully',
                'data' => [
                    'user' => [
                        'id' => 1,
                        'name' => 'Pendeta Dummy',
                        'email' => 'pendeta@gpdi.local',
                        'role' => 'pendeta',
                    ],
                ],
                'meta' => null,
            ], 200),

            'http://localhost:8002/api/content/announcements' => Http::response([
                'status' => 'success',
                'message' => 'Announcements retrieved successfully',
                'data' => [],
                'meta' => null,
            ], 200),
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer valid-token',
        ])->getJson('/api/content/announcements');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Announcements retrieved successfully',
            ]);
    }

    public function test_unknown_route_returns_not_found(): void
    {
        $response = $this->getJson('/api/some-random-route');

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Resource not found',
            ]);
    }

    public function test_downstream_failure_returns_internal_server_error(): void
    {
        config()->set('services.content_service.base_url', 'http://localhost:8002');

        Http::fake([
            'http://localhost:8002/api/public/announcements' => function () {
                throw new \Exception('Service unavailable');
            },
        ]);

        $response = $this->getJson('/api/public/announcements');

        $response->assertStatus(500)
            ->assertJson([
                'status' => 'error',
                'message' => 'Internal server error',
            ]);
    }
}