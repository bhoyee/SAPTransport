<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class TrackLastUrl
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && !session()->has('is_locked')) {
            // Store the current URL as the previous URL if not locked
            session()->put('previousUrl', $request->url());
        }

        return $next($request);
    }
}