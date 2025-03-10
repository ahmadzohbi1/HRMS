<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {

                if (Auth::guard($guard)->user()->is_admin == 1) {
                    return redirect(RouteServiceProvider::HOME);
                } elseif (Auth::guard($guard)->user()->is_admin == 0) {
                    return redirect()->route("front.index");
                } else {
                    return redirect()->route("profile");
                }
            }
        }

        return $next($request);
    }
}
