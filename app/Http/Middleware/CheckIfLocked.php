<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckIfLocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    protected $lockTimeout = 60; // 10 minutes in seconds

    public function handle($request, Closure $next)
    {
        $lastActivity = session()->get('lastActivityTime');

        if (Auth::check() && now()->timestamp - $lastActivity > $this->lockTimeout) {
            // Set the is_locked session value to true when the user is inactive
            session()->put('is_locked', true);
            return redirect()->route('lockscreen.show');
        }

        // Update the last activity time
        session()->put('lastActivityTime', now()->timestamp);

        return $next($request);
    }
}