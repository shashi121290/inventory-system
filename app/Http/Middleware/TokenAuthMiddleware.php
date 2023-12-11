<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');

        // Replace 'test-static-token' with your actual static token
        if ($token !== 'Bearer test-static-token') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
