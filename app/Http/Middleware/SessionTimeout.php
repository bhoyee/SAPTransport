<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    protected $timeout = 1200; // 20 minutes in seconds


    public function handle(Request $request, Closure $next): Response
    {

        if (Auth::check()) {
            $lastActivity = session()->get('lastActivityTime');

            // If session has expired after 20 minutes
            if (now()->timestamp - $lastActivity > $this->timeout) {
                Auth::logout();
                session()->flush(); // Clear session data
                return redirect()->route('login')->with('message', 'Your session has expired due to inactivity.');
            }

            // Update last activity time
            session()->put('lastActivityTime', now()->timestamp);
        }
        return $next($request);
    }
}
