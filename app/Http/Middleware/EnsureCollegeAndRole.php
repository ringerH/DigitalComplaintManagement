<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureCollegeAndRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('web')->user();
        if (!$user->college_id) {
            return redirect()->route('select-college');
        }
        if (!$user->usertype) {
            return redirect()->route('select-role');
        }
        return $next($request);
    }
}