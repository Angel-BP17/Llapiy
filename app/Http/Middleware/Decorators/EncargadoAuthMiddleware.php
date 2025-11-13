<?php

namespace App\Http\Middleware\Decorators;

use App\Http\Middleware\Contracts\AuthMiddleware;
use Auth;
use Closure;
use Illuminate\Http\Request;

class EncargadoAuthMiddleware implements AuthMiddleware
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

        if (!in_array(Auth::user()->userType->name, ['Administrador', 'Revisor/Aprobador'])) {
            abort(403, 'Se requieren permisos de administrador o revisor/aprobador');
        }

        return $response;
    }
}