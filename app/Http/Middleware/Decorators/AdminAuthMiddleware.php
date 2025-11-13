<?php

namespace App\Http\Middleware\Decorators;

use App\Http\Middleware\Contracts\AuthMiddleware;
use Auth;
use Closure;
use Illuminate\Http\Request;

class AdminAuthMiddleware implements AuthMiddleware
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

        if (Auth::user()->userType->name !== 'Administrador') {
            abort(403, 'No tienes permisos de administrador');
        }

        return $response;
    }
}