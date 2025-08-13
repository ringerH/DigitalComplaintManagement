<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NonAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('web')->check() && Auth::guard('web')->user()->usertype === 'admin') {
            return redirect()->route('admin.login')->with('error', 'Admin access not allowed here.');
        }
        return $next($request);
    }
}