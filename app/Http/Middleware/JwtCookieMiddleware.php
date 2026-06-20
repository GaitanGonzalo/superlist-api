<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class JwtCookieMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasCookie('AUTH_TOKEN')) {
            $token = $request->cookie('AUTH_TOKEN');
            $request->headers->set('Authorization', 'Bearer ' . $token);
        }
        Log::info('Request INFO', [
            'User IP' => $request->ip(),
            'user Agent' => $request->userAgent(),
            'path' => $request->path()
        ]);
        Log::info('JWT Cookie Middleware ejecutado', [
            'cookie' => $request->cookie('AUTH_TOKEN') ? true : false,
            'auth_header' => $request->header('Authorization')? true : false
        ]);
        return $next($request);
    }
}
