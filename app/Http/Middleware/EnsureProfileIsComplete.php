<?php

namespace App\Http\Middleware;

use App\Models\Profile;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    /**
     * Routes that are always accessible even without a profile.
     * These are the profile creation routes + logout.
     */
    protected array $except = [
        'staff.profile.create',
        'staff.profile.store',
        'staff.profile.upload_avatar',
        'staff.auth.logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Only applies to authenticated users
        if (!Auth::check()) {
            return $next($request);
        }

        // Allow whitelisted routes through always
        foreach ($this->except as $routeName) {
            if ($request->routeIs($routeName)) {
                return $next($request);
            }
        }

        // Check if the user has a profile
        $hasProfile = Profile::where('user_id', Auth::id())->exists();

        if (!$hasProfile) {
            return redirect()->route('staff.profile.create')
                ->with('error', 'Please complete your profile before accessing the portal.');
        }

        return $next($request);
    }
}