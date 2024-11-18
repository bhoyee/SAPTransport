<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;


class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        Log::info('Redirecting unauthenticated user to: home');
        if (!$request->expectsJson()) {
            return route('home');
        }
    }
    
    
}
