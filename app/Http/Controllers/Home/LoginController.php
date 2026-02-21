<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Services\Home\LoginService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct(protected LoginService $service)
    {
    }

    public function __invoke()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('user_name', 'password');

        if ($this->service->attempt($credentials)) {
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'loginError' => 'Credenciales invalidas. Por favor, intentalo de nuevo.',
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user && method_exists($user, 'currentAccessToken') && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();

            return $this->apiSuccess('Sesion cerrada correctamente.');
        }

        $this->service->logout($request);

        return $this->apiSuccess('Sesion cerrada correctamente.');
    }

    public function loginApi(Request $request)
    {
        $credentials = $request->validate([
            'user_name' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!$this->service->attempt($credentials)) {
            return $this->apiError('Credenciales invalidas.', 401);
        }

        $user = Auth::user();
        $token = $user?->createToken('api-token')->plainTextToken;

        if ($user === null || $token === null) {
            return $this->apiError('No se pudo generar el token de acceso.', 500);
        }

        return $this->apiSuccess('Autenticacion exitosa.', [
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }
}
