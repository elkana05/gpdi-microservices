<?php

namespace Tests\Unit;

use App\Services\ServiceRegistry;
use Tests\TestCase;

class ServiceRegistryTest extends TestCase
{
    public function test_it_resolves_user_service_routes(): void
    {
        config()->set('services.user_service.base_url', 'http://localhost:8001');

        $this->assertEquals(
            'http://localhost:8001',
            ServiceRegistry::resolve('api/auth/login')
        );

        $this->assertEquals(
            'http://localhost:8001',
            ServiceRegistry::resolve('api/auth/me')
        );

        $this->assertEquals(
            'http://localhost:8001',
            ServiceRegistry::resolve('api/user/profile')
        );

        $this->assertEquals(
            'http://localhost:8001',
            ServiceRegistry::resolve('api/users/1')
        );

        $this->assertEquals(
            'http://localhost:8001',
            ServiceRegistry::resolve('api/family-members')
        );
    }

    public function test_it_resolves_content_service_routes(): void
    {
        config()->set('services.content_service.base_url', 'http://localhost:8002');

        $this->assertEquals(
            'http://localhost:8002',
            ServiceRegistry::resolve('api/public/homepage')
        );

        $this->assertEquals(
            'http://localhost:8002',
            ServiceRegistry::resolve('api/public/announcements')
        );

        $this->assertEquals(
            'http://localhost:8002',
            ServiceRegistry::resolve('api/content/announcements')
        );
    }

    public function test_it_resolves_event_service_routes(): void
    {
        config()->set('services.event_service.base_url', 'http://localhost:8003');

        $this->assertEquals(
            'http://localhost:8003',
            ServiceRegistry::resolve('api/public/worship-schedules')
        );

        $this->assertEquals(
            'http://localhost:8003',
            ServiceRegistry::resolve('api/public/activity-schedules')
        );

        $this->assertEquals(
            'http://localhost:8003',
            ServiceRegistry::resolve('api/event/rayon-schedules')
        );
    }

    public function test_it_resolves_admin_service_routes(): void
    {
        config()->set('services.admin_service.base_url', 'http://localhost:8004');

        $this->assertEquals(
            'http://localhost:8004',
            ServiceRegistry::resolve('api/admin/notifications')
        );

        $this->assertEquals(
            'http://localhost:8004',
            ServiceRegistry::resolve('api/admin/letter-requests')
        );
    }

    public function test_it_returns_null_for_unknown_route(): void
    {
        $this->assertNull(ServiceRegistry::resolve('api/unknown/path'));
    }

    public function test_it_returns_service_name_correctly(): void
    {
        $this->assertEquals('user_service', ServiceRegistry::serviceName('api/auth/login'));
        $this->assertEquals('content_service', ServiceRegistry::serviceName('api/content/announcements'));
        $this->assertEquals('event_service', ServiceRegistry::serviceName('api/event/rayon-schedules'));
        $this->assertEquals('admin_service', ServiceRegistry::serviceName('api/admin/notifications'));
        $this->assertNull(ServiceRegistry::serviceName('api/unknown/path'));
    }
}