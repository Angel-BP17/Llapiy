<?php

namespace Tests\Feature\Security;

use App\Models\Block;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class BlockLifecyclePolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_common_user_can_update_and_delete_their_own_block_without_pdf(): void
    {
        $user = User::factory()->create();
        $user->assignRole('COLABORADOR_DOCUMENTAL');

        $block = Block::factory()->create([
            'user_id' => $user->id,
            'root' => null,
            'asunto' => 'Bloque Original',
            'folios' => 10,
        ]);

        $this->assertTrue($user->can('update', $block));
        $this->assertTrue($user->can('delete', $block));

        $updateResponse = $this->actingAs($user)->put("/bloques/{$block->id}", [
            'asunto' => 'Bloque Modificado',
            'folios' => '20',
            'fecha' => '2026-09-14',
            'rango_inicial' => 1,
            'rango_final' => 20,
        ]);

        $updateResponse->assertSessionHas('message', 'Bloque actualizado correctamente.');
        $this->assertDatabaseHas('blocks', [
            'id' => $block->id,
            'asunto' => 'Bloque Modificado',
        ]);

        $deleteResponse = $this->actingAs($user)->delete("/bloques/{$block->id}");
        $deleteResponse->assertSessionHas('message', 'Bloque eliminado correctamente.');
        $this->assertDatabaseMissing('blocks', ['id' => $block->id]);
    }

    public function test_common_user_cannot_update_or_delete_block_when_it_has_pdf(): void
    {
        $user = User::factory()->create();
        $user->assignRole('COLABORADOR_DOCUMENTAL');

        $block = Block::factory()->create([
            'user_id' => $user->id,
            'root' => 'blocks/expediente_digitalizado.pdf',
            'asunto' => 'Bloque con PDF',
            'folios' => '10',
        ]);

        $this->assertFalse($user->can('update', $block));
        $this->assertFalse($user->can('delete', $block));

        $updateResponse = $this->actingAs($user)->put("/bloques/{$block->id}", [
            'asunto' => 'Intento Modificar',
            'folios' => '30',
            'fecha' => '2026-09-14',
            'rango_inicial' => 1,
            'rango_final' => 30,
        ]);

        $updateResponse->assertSessionHas('error', 'Ocurrio un error al editar el bloque.');
        $this->assertDatabaseHas('blocks', [
            'id' => $block->id,
            'asunto' => 'Bloque con PDF',
        ]);

        $deleteResponse = $this->actingAs($user)->delete("/bloques/{$block->id}");
        $deleteResponse->assertSessionHas('error', 'Ocurrio un error al eliminar el bloque.');
        $this->assertDatabaseHas('blocks', ['id' => $block->id]);
    }

    public function test_archivo_central_can_update_and_delete_block_with_pdf(): void
    {
        $archivoUser = User::factory()->create();
        $archivoUser->assignRole('ARCHIVO_CENTRAL');

        $block = Block::factory()->create([
            'root' => 'blocks/expediente_digitalizado.pdf',
            'asunto' => 'Bloque Digitalizado',
            'folios' => '15',
        ]);

        $this->assertTrue($archivoUser->can('update', $block));
        $this->assertTrue($archivoUser->can('delete', $block));

        $updateResponse = $this->actingAs($archivoUser)->put("/bloques/{$block->id}", [
            'asunto' => 'Bloque Actualizado por Archivo',
            'folios' => '18',
            'fecha' => '2026-09-14',
            'rango_inicial' => 1,
            'rango_final' => 18,
        ]);

        $updateResponse->assertSessionHas('message', 'Bloque actualizado correctamente.');

        $deleteResponse = $this->actingAs($archivoUser)->delete("/bloques/{$block->id}");
        $deleteResponse->assertSessionHas('message', 'Bloque eliminado correctamente.');
        $this->assertDatabaseMissing('blocks', ['id' => $block->id]);
    }

    public function test_administrador_can_update_and_delete_block_with_pdf(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('ADMINISTRADOR');

        $block = Block::factory()->create([
            'root' => 'blocks/expediente_digitalizado.pdf',
            'asunto' => 'Bloque para Admin',
            'folios' => '15',
        ]);

        $this->assertTrue($admin->can('update', $block));
        $this->assertTrue($admin->can('delete', $block));

        $updateResponse = $this->actingAs($admin)->put("/bloques/{$block->id}", [
            'asunto' => 'Bloque Actualizado por Admin',
            'folios' => '18',
            'fecha' => '2026-09-14',
            'rango_inicial' => 1,
            'rango_final' => 18,
        ]);

        $updateResponse->assertSessionHas('message', 'Bloque actualizado correctamente.');

        $deleteResponse = $this->actingAs($admin)->delete("/bloques/{$block->id}");
        $deleteResponse->assertSessionHas('message', 'Bloque eliminado correctamente.');
        $this->assertDatabaseMissing('blocks', ['id' => $block->id]);
    }

    public function test_post_too_large_exception_redirects_with_friendly_error(): void
    {
        Route::middleware(['web'])->post('/test-post-too-large', function () {
            throw new PostTooLargeException;
        });

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/test-post-too-large', []);

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'El archivo adjunto excede el tamaño máximo permitido por el servidor.');
    }
}
