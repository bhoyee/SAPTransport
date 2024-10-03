<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{
    protected $timeout = 600; // 10 minutes in seconds

    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $lastActivity = session()->get('lastActivityTime');

            // If session has expired
            if (now()->timestamp - $lastActivity > $this->timeout) {
                Auth::logout();
                session()->flush(); // Clear session data
                return redirect()->route('login')->with('message', 'Your session has expired due to inactivity.');
            }

            session()->put('lastActivityTime', now()->timestamp);
        }

        return $next($request);
    }
}
