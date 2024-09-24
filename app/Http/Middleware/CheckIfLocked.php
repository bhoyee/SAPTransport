<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckIfLocked
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
        // Define routes that are excluded from the lock screen check
        $excludedRoutes = ['about', 'faq', 'contact', '/', 'login', 'lock', 'register', 'password/reset'];

        // Check if the route is one of the excluded routes
        if (!in_array($request->path(), $excludedRoutes)) {
            // Check if the user is logged in and their session is locked
            if (auth()->check() && session('is_locked')) {
                return redirect()->route('lockscreen.show');
            }
        }

        return $next($request);
    }
}
