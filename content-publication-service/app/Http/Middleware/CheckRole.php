<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Menerima role dari header X-Role yang dikirim API Gateway
     * setelah Auth Service memverifikasi token JWT.
     * Contoh: X-Role: pendeta | X-Role: jemaat | X-Role: ketua_rayon
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $userRole = $request->header('X-Role');

        if (!$userRole || !in_array($userRole, $roles)) {
            return response()->json(['message' => 'Akses ditolak. Role tidak sesuai.'], 403);
        }

        return $next($request);
    }
}
