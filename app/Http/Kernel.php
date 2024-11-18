<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;


class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\SessionTimeout::class, // Ensure this runs early
            \Illuminate\Session\Middleware\StartSession::class,

            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
  

            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
  
            \Illuminate\Auth\Middleware\Authenticate::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\TrackLastUrl::class,

            // Additional middleware for the web routes
            \App\Http\Middleware\CheckIfLocked::class,  // Custom lock screen middleware
            \App\Http\Middleware\CheckRole::class, // Add this line
           
            \App\Http\Middleware\TrackLastUrl::class, // Add this line


        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'session.timeout' => \App\Http\Middleware\SessionTimeout::class, // Custom session timeout middleware

        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'lock' => \App\Http\Middleware\CheckIfLocked::class, // Custom lock middleware
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class, // Laravel provided middleware
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class, // Laravel provided middleware
        'can' => \Illuminate\Auth\Middleware\Authorize::class, // Laravel provided middleware
        // 'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class, // Custom middleware
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class, // Laravel provided middleware
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class, // Laravel provided middleware
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class, // Laravel provided middleware
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class, // Laravel provided middleware

    ];
}
