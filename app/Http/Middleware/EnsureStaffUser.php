<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffUser
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Allow access if user is staff or uses official masagana.ph domain
        if (! $user || ! ($user->role === 'staff' || str_ends_with($user->email, '@masagana.ph') || (isset($user->is_staff) && $user->is_staff))) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access to Staff Portal.');
        }

        return $next($request);
    }
}
