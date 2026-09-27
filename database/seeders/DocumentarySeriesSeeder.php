<?php

namespace Database\Seeders;

use App\Models\DocumentarySeries;
use Illuminate\Database\Seeder;

class DocumentarySeriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $series = [
            [
                'codigo' => '01.01',
                'nombre' => 'Resoluciones Directorales',
            ],
            [
                'codigo' => '01.02',
                'nombre' => 'Resoluciones Jefaturales',
            ],
            [
                'codigo' => '01.03',
                'nombre' => 'Oficios Múltiples',
            ],
            [
                'codigo' => '01.04',
                'nombre' => 'Oficios Circulares',
            ],
            [
                'codigo' => '01.05',
                'nombre' => 'Memorandos y Memorandos Múltiples',
            ],
            [
                'codigo' => '01.06',
                'nombre' => 'Informes Técnicos y Pedagógicos',
            ],
            [
                'codigo' => '02.01',
                'nombre' => 'Expedientes de Contratación de Personal (Docente, Administrativo y CAS)',
            ],
            [
                'codigo' => '02.02',
                'nombre' => 'Legajos Personales del Personal Activo y Cesante',
            ],
            [
                'codigo' => '02.03',
                'nombre' => 'Planillas Únicas de Pagos y Remuneraciones',
            ],
            [
                'codigo' => '02.04',
                'nombre' => 'Expedientes de Licencias, Permisos y Subsidios',
            ],
            [
                'codigo' => '02.05',
                'nombre' => 'Expedientes de Procesos Administrativos Disciplinarios (PAD)',
            ],
            [
                'codigo' => '03.01',
                'nombre' => 'Expedientes de Contrataciones y Adquisiciones del Estado',
            ],
            [
                'codigo' => '03.02',
                'nombre' => 'Comprobantes de Pago y Rendición de Cuentas',
            ],
            [
                'codigo' => '03.03',
                'nombre' => 'Balances y Estados Financieros y Presupuestales',
            ],
            [
                'codigo' => '04.01',
                'nombre' => 'Actas de Sesiones, Comités y Consejos Directivos',
            ],
            [
                'codigo' => '04.02',
                'nombre' => 'Convenios de Cooperación Interinstitucional',
            ],
            [
                'codigo' => '05.01',
                'nombre' => 'Actas Oficiales de Evaluación y Certificación de Estudios',
            ],
            [
                'codigo' => '05.02',
                'nombre' => 'Proyectos y Planes de Monitoreo Pedagógico',
            ],
        ];

        foreach ($series as $data) {
            DocumentarySeries::updateOrCreate(
                ['codigo' => $data['codigo']],
                ['nombre' => $data['nombre']]
            );
        }
    }
}
