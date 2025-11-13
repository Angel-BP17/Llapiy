<?php

namespace App\Http\Middleware\Decorators;

use App\Http\Middleware\Contracts\AuthMiddleware;
use Auth;
use Closure;
use Illuminate\Http\Request;

class UserAuthMiddleware implements AuthMiddleware
{
    protected $middleware;

    public function __construct(AuthMiddleware $middleware)
    {
        $this->middleware = $middleware;
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $this->middleware->handle($request, $next);

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Todos los usuarios autenticados pasan
        return $response;
    }
}