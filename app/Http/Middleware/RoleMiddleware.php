<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!auth()->check() || !auth()->user()->hasRole($role)) {
            // Redirect if the user does not have the required role
            return view('admin.errors.404'); // You can change this to any redirect URL
        }

        return $next($request);
    }
}
