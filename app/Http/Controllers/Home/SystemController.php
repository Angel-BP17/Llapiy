<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Services\Home\SystemService;
use Exception;
use Illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemController extends Controller
{
    public function __construct(protected SystemService $service)
    {
    }

    /**
     * Clear all system data and create defaults.
     */
    public function clearAll(Request $request): RedirectResponse
    {
        try {
            $this->service->clearAll();

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('message', 'Datos eliminados y predeterminados creados. Inicie sesión con la cuenta de administrador.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error al borrar datos: ' . $e->getMessage());
        }
    }
}
