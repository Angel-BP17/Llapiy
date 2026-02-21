<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Requests\Home\IndexHomeRequest;
use App\Services\Home\HomeService;

class HomeController extends Controller
{
    public function __construct(protected HomeService $service)
    {
    }

    public function index(IndexHomeRequest $request)
    {
        return $this->apiSuccess('Dashboard obtenido correctamente.', $this->service->getDashboardData());
    }

    public function seedDefaults()
    {
        try {
            $this->service->seedDefaults();

            return $this->apiSuccess('Datos predeterminados insertados correctamente.');
        } catch (\Throwable $e) {
            return $this->apiError('Error al ejecutar seeding: ' . $e->getMessage(), 500);
        }
    }
}
