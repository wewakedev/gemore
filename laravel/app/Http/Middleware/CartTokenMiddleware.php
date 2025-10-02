<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CartTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if cart_token cookie exists
        if (!$request->cookie('cart_token')) {
            // Generate a new UUID for cart token
            $cartToken = Str::uuid()->toString();
            
            // Create response and set cookie
            $response = $next($request);
            
            // Set cookie for 30 days using Cookie facade
            $response->withCookie('cart_token', $cartToken, 60 * 24 * 30);
            
            return $response;
        }

        return $next($request);
    }
}
