<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{


    public function handle($request, Closure $next, $role)
    {
        if (! $request->user() || ! $request->user()->hasRole($role)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }

}
