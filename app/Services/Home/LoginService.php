<?php

namespace App\Services\Home;

use Auth;
use Illuminate\Http\Request;

class LoginService
{
    public function attempt(array $credentials): bool
    {
        return Auth::attempt($credentials);
    }

    public function logout(Request $request): void
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
    }
}

