<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle($request, Closure $next, $role)
    {
        // Check if the authenticated user has the required role
        if (Auth::check() && Auth::user()->role === $role) {
            return $next($request);
        }

        // If user doesn't have the required role, redirect to a forbidden or login page
        return redirect()->route('login')->with('error', 'Unauthorized access.');
    }
}
