<?php

namespace App\Services\Home;

use Auth;
use Illuminate\Http\Request;

class LoginService
{
    /**
     * Intenta autenticar al usuario con las credenciales proporcionadas.
     */
    public function attempt(array $credentials): bool
    {
        return Auth::attempt($credentials);
    }

    /**
     * Cierra la sesión del usuario y limpia los tokens de seguridad.
     */
    public function logout(Request $request): void
    {
        if (Auth::check()) {
            Auth::user()->tokens()->delete(); // Revoca tokens de Sanctum si existen
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
    }
}
