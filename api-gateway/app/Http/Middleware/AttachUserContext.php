<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AttachUserContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $authUser = $request->attributes->get('auth_user');

        if ($authUser) {
            $request->attributes->set('gateway_user_id', $authUser['id'] ?? null);
            $request->attributes->set('gateway_user_role', $authUser['role'] ?? null);
            $request->attributes->set('gateway_user_email', $authUser['email'] ?? null);
            $request->attributes->set('gateway_user_name', $authUser['name'] ?? null);
        }

        return $next($request);
    }
}