<?php

namespace App\Http\Controllers\Home;

use App\Models\User;
use Auth;
use Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __invoke()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        $user = User::where('user_name', $credentials['username'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Iniciar sesión si las credenciales son correctas
            session_start();
            Auth::login($user);
            $_SESSION['usuario'] = request()->only('username');
            return redirect()->intended('/'); // Redirigir al dashboard o página deseada
        } else {
            // Si las credenciales son incorrectas
            return back()->withErrors([
                'loginError' => 'Credenciales inválidas. Por favor, inténtelo de nuevo.',
            ])->withInput($request->except('password'));
        }
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login');
        }

        return redirect('/login');
    }
}
