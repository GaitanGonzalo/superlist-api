<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
class OnlyAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            
            if(!$request->user()->isAdmin()) return response()->json('unauthorized', 401);
        } catch (\Throwable $th) {
            Log::info('Error MW-002', ['message'=> $th->getMessage()]);
        }
        return $next($request);
    }
}
