<?php

namespace App\Services;

class ServiceRegistry
{
    public static function resolve(string $path): ?string
    {
        $path = ltrim($path, '/');

        return match (true) {
            // User Service
            str_starts_with($path, 'api/auth') => config('services.user_service.base_url'),
            str_starts_with($path, 'api/user') => config('services.user_service.base_url'),
            str_starts_with($path, 'api/users') => config('services.user_service.base_url'),
            str_starts_with($path, 'api/family-members') => config('services.user_service.base_url'),

            // Content Service - Public
            str_starts_with($path, 'api/public/homepage') => config('services.content_service.base_url'),
            str_starts_with($path, 'api/public/church-profile') => config('services.content_service.base_url'),
            str_starts_with($path, 'api/public/service-information') => config('services.content_service.base_url'),
            str_starts_with($path, 'api/public/galleries') => config('services.content_service.base_url'),
            str_starts_with($path, 'api/public/contact-location') => config('services.content_service.base_url'),
            str_starts_with($path, 'api/public/announcements') => config('services.content_service.base_url'),
            str_starts_with($path, 'api/public/devotionals/latest') => config('services.content_service.base_url'),

            // Content Service - Private
            str_starts_with($path, 'api/content') => config('services.content_service.base_url'),

            // Event Service - Public
            str_starts_with($path, 'api/public/worship-schedules') => config('services.event_service.base_url'),
            str_starts_with($path, 'api/public/activity-schedules') => config('services.event_service.base_url'),

            // Event Service - Private
            str_starts_with($path, 'api/event') => config('services.event_service.base_url'),

            // Admin Service
            str_starts_with($path, 'api/admin') => config('services.admin_service.base_url'),

            default => null,
        };
    }

    public static function serviceName(string $path): ?string
    {
        $path = ltrim($path, '/');

        return match (true) {
            str_starts_with($path, 'api/auth'),
            str_starts_with($path, 'api/user'),
            str_starts_with($path, 'api/users'),
            str_starts_with($path, 'api/family-members') => 'user_service',

            str_starts_with($path, 'api/content'),
            str_starts_with($path, 'api/public/homepage'),
            str_starts_with($path, 'api/public/church-profile'),
            str_starts_with($path, 'api/public/service-information'),
            str_starts_with($path, 'api/public/galleries'),
            str_starts_with($path, 'api/public/contact-location'),
            str_starts_with($path, 'api/public/announcements'),
            str_starts_with($path, 'api/public/devotionals/latest') => 'content_service',

            str_starts_with($path, 'api/event'),
            str_starts_with($path, 'api/public/worship-schedules'),
            str_starts_with($path, 'api/public/activity-schedules') => 'event_service',

            str_starts_with($path, 'api/admin') => 'admin_service',

            default => null,
        };
    }
}