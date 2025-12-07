<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // If user doesn't have role, default to 'client' for backward compatibility
        if (!$user->role) {
            $user->update(['role' => 'client']);
            $user->refresh();
        }
        
        // Allow multiple roles separated by |
        $allowedRoles = explode('|', $role);
        
        if (!in_array($user->role, $allowedRoles)) {
            abort(403, 'Unauthorized access. Required role(s): ' . $role . ', Your role: ' . ($user->role ?? 'none'));
        }

        return $next($request);
    }
}

