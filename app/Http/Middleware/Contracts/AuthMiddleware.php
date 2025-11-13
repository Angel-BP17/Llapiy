<?php

namespace App\Http\Middleware\Contracts;

use Closure;
use Illuminate\Http\Request;

interface AuthMiddleware
{
    public function handle(Request $request, Closure $next);
}