<?php

namespace App\Http\Middleware;

use App\Http\Middleware\Decorators\AdminAuthMiddleware;
use App\Http\Middleware\Decorators\EncargadoAuthMiddleware;
use App\Http\Middleware\Decorators\UserAuthMiddleware;

class AuthMiddlewareFactory
{
    public static function make(string $roleType)
    {
        $base = new BaseAuthMiddleware();

        switch ($roleType) {
            case 'admin':
                return new AdminAuthMiddleware($base);
            case 'encargado':
                return new EncargadoAuthMiddleware($base);
            case 'user':
                return new UserAuthMiddleware($base);
            case 'base':
                return new BaseAuthMiddleware();
            default:
                throw new \InvalidArgumentException("Tipo de rol no soportado");
        }
    }
}