<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SystemClearAllTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('ADMINISTRADOR');

        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole('COLABORADOR_DOCUMENTAL');
    }

    public function test_unauthorized_user_cannot_clear_system(): void
    {
        $response = $this->actingAs($this->regularUser)->delete('/admin/clear-all');

        $response->assertStatus(403);
    }

    public function test_admin_can_clear_system_and_wipes_private_storage_files(): void
    {
        // 1. Simular archivos en el almacenamiento privado (local) y público
        $privateBlockFile = 'blocks/test-area/sample_block.pdf';
        $privateDocFile = 'documents/test-area/sample_doc.pdf';
        $privateProfileFile = 'usuarios/perfiles/sample_avatar.jpg';
        $publicDocFile = 'documents/legacy_doc.pdf';

        Storage::disk('local')->put($privateBlockFile, 'fake block content');
        Storage::disk('local')->put($privateDocFile, 'fake doc content');
        Storage::disk('local')->put($privateProfileFile, 'fake profile content');
        Storage::disk('public')->put($publicDocFile, 'fake public content');

        $this->assertTrue(Storage::disk('local')->exists($privateBlockFile));
        $this->assertTrue(Storage::disk('local')->exists($privateDocFile));
        $this->assertTrue(Storage::disk('local')->exists($privateProfileFile));
        $this->assertTrue(Storage::disk('public')->exists($publicDocFile));

        // 2. Ejecutar la acción de limpiar sistema
        $response = $this->actingAs($this->admin)->delete('/admin/clear-all');

        // 3. Verificar redirección y mensaje
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('message');

        // 4. Verificar que el usuario fue desautenticado
        $this->assertGuest();

        // 5. Verificar que los archivos del almacenamiento privado y público fueron completamente eliminados
        $this->assertFalse(Storage::disk('local')->exists($privateBlockFile));
        $this->assertFalse(Storage::disk('local')->exists($privateDocFile));
        $this->assertFalse(Storage::disk('local')->exists($privateProfileFile));
        $this->assertFalse(Storage::disk('public')->exists($publicDocFile));
    }
}
