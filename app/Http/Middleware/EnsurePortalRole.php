<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePortalRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        $allowed = $user
            && in_array($user->role, ['admin', 'developer'], true)
            && in_array($role, ['admin', 'developer'], true);

        if (! $allowed) {
            abort(403, 'You are not authorized to access this portal.');
        }

        return $next($request);
    }
}
