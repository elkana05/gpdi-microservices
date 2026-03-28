<?php

return [

    'user_service' => [
        'base_url' => env('USER_SERVICE_URL', 'http://localhost:8001'),
    ],

    'content_service' => [
        'base_url' => env('CONTENT_SERVICE_URL', 'http://localhost:8002'),
    ],

    'event_service' => [
        'base_url' => env('EVENT_SERVICE_URL', 'http://localhost:8003'),
    ],

    'admin_service' => [
        'base_url' => env('ADMIN_SERVICE_URL', 'http://localhost:8004'),
    ],

    'gateway' => [
        'timeout' => env('GATEWAY_TIMEOUT', 10),
        'connect_timeout' => env('GATEWAY_CONNECT_TIMEOUT', 3),
    ],

];