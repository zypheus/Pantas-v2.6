<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            abort(403, 'Not authenticated.');
        }

        if (!in_array(Auth::user()->role, $roles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
