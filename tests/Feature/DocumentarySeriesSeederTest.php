<?php

namespace Tests\Feature;

use App\Models\DocumentarySeries;
use Database\Seeders\DocumentarySeriesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentarySeriesSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_documentary_series_successfully(): void
    {
        $this->seed(DocumentarySeriesSeeder::class);

        $this->assertDatabaseHas('documentary_series', [
            'codigo' => '01.01',
            'nombre' => 'Resoluciones Directorales',
        ]);

        $this->assertDatabaseHas('documentary_series', [
            'codigo' => '02.01',
            'nombre' => 'Expedientes de Contratación de Personal (Docente, Administrativo y CAS)',
        ]);

        $this->assertGreaterThan(0, DocumentarySeries::count());
    }

    public function test_it_is_idempotent_when_run_multiple_times(): void
    {
        $this->seed(DocumentarySeriesSeeder::class);
        $initialCount = DocumentarySeries::count();

        // Seed a second time
        $this->seed(DocumentarySeriesSeeder::class);
        $secondCount = DocumentarySeries::count();

        $this->assertSame($initialCount, $secondCount);
    }
}
