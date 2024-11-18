<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SessionTimeout
{
    protected $timeout = 120; // Total session timeout (2 minutes)
    protected $lockTime = 60; // Lock screen timeout trigger (1 minute)

    public function handle(Request $request, Closure $next)
    {
        if ($request->routeIs('login') || $request->ajax()) {
            // Skip checks for login page and AJAX requests
            return $next($request);
        }

        if (Auth::check()) {
            $lastActivity = session('lastActivityTime');
            $currentTime = now()->timestamp;

            Log::info('Middleware Check:');
            Log::info('Last Activity: ' . $lastActivity);
            Log::info('Current Time: ' . $currentTime);

            if ($lastActivity) {
                $inactiveTime = $currentTime - $lastActivity;
                Log::info('Inactive Time: ' . $inactiveTime . ' seconds');

                // If total timeout is exceeded, log out and redirect to home page
                if ($inactiveTime > $this->timeout) {
                    Log::info('Session timeout reached. Redirecting to home.');
                    Auth::logout();
                    session()->flush();
                    return redirect()->route('home')->with('message', 'Session expired. Please log in again.');
                }

                // If lock screen timeout is exceeded but total timeout isn't, redirect to lock screen
                if ($inactiveTime > $this->lockTime) {
                    if ($request->routeIs('lockscreen.show')) {
                        Log::info('User idle on lock screen. Redirecting to home page.');
                        Auth::logout();
                        session()->flush();
                        return redirect()->route('home')->with('message', 'Session expired. Please log in again.');
                    }

                    Log::info('Redirecting to lock screen due to inactivity.');
                    return redirect()->route('lockscreen.show');
                }
            }

            // Update session activity if not on lock screen
            if (!$request->routeIs('lockscreen.show')) {
                session(['lastActivityTime' => $currentTime]);
                Log::info('Updated lastActivityTime in session.');
            }
        }

        return $next($request);
    }
}
