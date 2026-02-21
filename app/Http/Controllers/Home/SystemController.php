<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Services\Home\SystemService;
use Exception;

class SystemController extends Controller
{
    public function __construct(protected SystemService $service)
    {
    }

    public function clearAll()
    {
        try {
            $this->service->clearAll();

            return $this->apiSuccess('Datos eliminados y predeterminados creados.');
        } catch (Exception $e) {
            return $this->apiError('Error al borrar datos: ' . $e->getMessage(), 500);
        }
    }
}
