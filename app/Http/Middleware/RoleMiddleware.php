<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware('role:admin,doctor')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if (empty($roles)) {
            return $next($request);
        }

        if (in_array($user->role, $roles) || $user->role === 'admin') {
            return $next($request);
        }

        abort(403);
    }
}
