<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Services\Home\LoginService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    public function __construct(protected LoginService $service)
    {
    }

    /**
     * Show the login form.
     */
    public function showLoginForm(): Response
    {
        return Inertia::render('auth/login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'user_name' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($this->service->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'user_name' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('user_name');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->service->logout($request);
        return redirect('/login');
    }
}
