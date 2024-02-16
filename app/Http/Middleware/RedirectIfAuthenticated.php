<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $guard = null): Response
    {
        if (Auth::guard($guard)->check()) {
            return redirect()->intended();
        }

        return $next($request);
    }
}
