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

        if (! $user || ! in_array($user->role, ['staff', 'admin', 'developer'], true)) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access to Staff Portal.');
        }

        return $next($request);
    }
}
