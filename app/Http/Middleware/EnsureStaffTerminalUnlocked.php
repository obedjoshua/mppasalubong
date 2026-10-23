<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffTerminalUnlocked
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Redirect to lock screen if terminal is locked and not already on lock/unlock routes
        if ($request->session()->get('staff_terminal_locked', false) &&
            ! $request->routeIs('staff.session.locked') &&
            ! $request->routeIs('staff.session.unlock')) {
            return redirect()->route('staff.session.locked');
        }

        return $next($request);
    }
}
