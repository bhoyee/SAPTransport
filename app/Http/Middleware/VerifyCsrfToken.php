<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;


class VerifyCsrfToken extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        try {
            \Log::info('VerifyCsrfToken middleware: Processing request', ['url' => $request->url()]);
            return parent::handle($request, $next);
        } catch (TokenMismatchException $exception) {
            \Log::error('CSRF token mismatch', ['url' => $request->url()]);
            return redirect()->route('login')->with('error', 'Your session has expired. Please log in again.');
        }
    }
    
    protected $except = [
        '/',
        '/home',
        '/about',
        '/faq',
        '/contact',
        '/login',
     
    ];
}
