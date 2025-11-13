<?php

namespace App\Http\Middleware;

use App\Http\Middleware\Contracts\AuthMiddleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BaseAuthMiddleware implements AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}