<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            // Check the guard from the route
            $guard = $request->route()->getAction('middleware')[1] ?? 'web'; // e.g., ['auth:admin', 'admin'] -> 'admin'
            if (strpos($guard, 'auth:') === 0) {
                $guard = explode(':', $guard)[1];
            }

            if ($guard === 'admin') {
                return route('admin.login');
            }
            return route('login');
        }
    }
}