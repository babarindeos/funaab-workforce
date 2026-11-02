<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Send signed-in users to their own portal.
                return match (Auth::guard($guard)->user()->role) {
                    'admin'            => redirect()->route('admin.dashboard.index'),
                    'identity'         => redirect()->route('identity.dashboard.index'),
                    'staff', 'manager' => redirect()->route('staff.dashboard.index'),
                    default            => $next($request),
                };
            }
        }

        return $next($request);
    }
}
