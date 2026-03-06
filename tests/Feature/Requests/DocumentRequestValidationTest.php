<?php

namespace Tests\Feature\Requests;

use App\Models\User;
use App\Models\Block;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Carbon\Carbon;

class DocumentRequestValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        Route::put('/test-block-update/{bloque}', function (\App\Http\Requests\Document\UpdateBlockRequest $request, Block $bloque) {
            return response()->json($request->validated());
        });
    }

    public function test_block_update_calculates_period_dynamically(): void
    {
        \Spatie\Permission\Models\Permission::findOrCreate('blocks.update', 'web');
        $admin = User::factory()->create();
        $admin->givePermissionTo('blocks.update');
        $this->actingAs($admin);
        
        $fecha = '2025-05-15';
        $periodo = Carbon::parse($fecha)->year;
        
        $block = Block::create([
            'n_bloque' => 'B-001',
            'fecha' => $fecha,
            'periodo' => $periodo,
            'asunto' => 'Original',
            'folios' => '10',
            'rango_inicial' => 1,
            'rango_final' => 10,
        ]);

        // Intentar actualizar con el mismo n_bloque y la misma fecha (debería pasar por el ignore)
        $response = $this->putJson("/test-block-update/{$block->id}", [
            'n_bloque' => 'B-001',
            'fecha' => $fecha,
            'asunto' => 'Editado',
            'folios' => '10',
            'rango_inicial' => 1,
            'rango_final' => 10,
        ]);

        $response->assertStatus(200);
        $this->assertEquals('B-001', $response->json('n_bloque'));
    }

    public function test_block_update_fails_on_duplicate_in_same_period(): void
    {
        \Spatie\Permission\Models\Permission::findOrCreate('blocks.update', 'web');

        $admin = User::factory()->create();
        $admin->givePermissionTo('blocks.update');
        $this->actingAs($admin);
        
        $fecha = '2025-05-15';
        $periodo = Carbon::parse($fecha)->year;
        
        Block::create([
            'n_bloque' => 'B-DUP',
            'fecha' => $fecha,
            'periodo' => $periodo,
            'asunto' => 'Ocupado',
            'folios' => '10',
            'rango_inicial' => 1,
            'rango_final' => 10,
        ]);

        $anotherBlock = Block::create([
            'n_bloque' => 'B-OTRO',
            'fecha' => $fecha,
            'periodo' => $periodo,
            'asunto' => 'Libre',
            'folios' => '10',
            'rango_inicial' => 11,
            'rango_final' => 20,
        ]);

        // Intentar cambiar B-OTRO a B-DUP en el mismo periodo
        $response = $this->putJson("/test-block-update/{$anotherBlock->id}", [
            'n_bloque' => 'B-DUP',
            'fecha' => $fecha,
            'asunto' => 'Intento duplicar',
            'folios' => '10',
            'rango_inicial' => 11,
            'rango_final' => 20,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['n_bloque']);
    }
}
