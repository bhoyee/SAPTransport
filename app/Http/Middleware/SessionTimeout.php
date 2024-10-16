<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    protected $timeout = 1200; // 20 minutes in seconds
    protected $lockTime = 600; // 10 minutes in seconds for lock

    public function handle(Request $request, Closure $next): Response
    {
        // Skip session timeout checks on lock screen route
        if ($request->routeIs('lockscreen.show')) {
            return $next($request); 
        }

        if (Auth::check()) {
            $lastActivity = session()->get('lastActivityTime');
            $currentTime = now()->timestamp;

            if ($lastActivity) {
                $inactiveTime = $currentTime - $lastActivity;

                // If session has expired after 20 minutes
                if ($inactiveTime > $this->timeout) {
                    Auth::logout();
                    session()->flush(); // Clear session data
                    return redirect('/')->with('message', 'Your session has expired due to inactivity.');
                }

                // If idle for more than 10 minutes but session still valid, show lock screen
                if ($inactiveTime > $this->lockTime && $inactiveTime <= $this->timeout) {
                    return redirect()->route('lockscreen.show');
                }
            }

            // Update last activity time for active session
            session()->put('lastActivityTime', $currentTime);
        }

        return $next($request);
    }
}
