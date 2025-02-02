<?php

namespace App\Http\Middleware;

use Closure;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!auth('api')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
            ], 401);
        }

        return $next($request);
    }
}
