<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\CampoType;
use App\Models\DocumentType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CamposControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $operatorUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'OPERADOR']);

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('ADMINISTRADOR');

        $this->operatorUser = User::factory()->create();
        $this->operatorUser->assignRole('OPERADOR');
    }

    /** @test */
    public function admin_can_access_campos_index()
    {
        $response = $this->actingAs($this->adminUser)->get('/campos');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('campos/index')
                ->has('campos')
                ->has('dataTypes')
            );
    }

    /** @test */
    public function operator_cannot_access_campos_index()
    {
        $response = $this->actingAs($this->operatorUser)->get('/campos');
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_create_a_campo()
    {
        $data = [
            'name' => 'Campo de Prueba',
            'data_type' => 'string',
            'is_nullable' => true,
            'length' => 100
        ];

        $response = $this->actingAs($this->adminUser)->post('/campos', $data);

        $response->assertRedirect();
        $response->assertSessionHas('message', 'Tipo de campo creado correctamente.');
        $this->assertDatabaseHas('campo_types', ['name' => 'Campo de Prueba']);
    }

    /** @test */
    public function campo_creation_fails_with_duplicate_name()
    {
        CampoType::create(['name' => 'Duplicado', 'data_type' => 'string']);

        $data = ['name' => 'Duplicado', 'data_type' => 'int'];

        $response = $this->actingAs($this->adminUser)->post('/campos', $data);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function admin_can_delete_a_campo()
    {
        $campo = CampoType::create(['name' => 'A Eliminar', 'data_type' => 'string']);

        $response = $this->actingAs($this->adminUser)->delete("/campos/{$campo->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('campo_types', ['id' => $campo->id]);
    }

    /** @test */
    public function cannot_delete_campo_in_use_by_document_type()
    {
        $campo = CampoType::create(['name' => 'En Uso', 'data_type' => 'string']);
        $docType = DocumentType::factory()->create();
        
        // Tabla pivote correcta segun el modelo CampoType
        \DB::table('campo_document_types')->insert([
            'document_type_id' => $docType->id,
            'campo_type_id' => $campo->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $response = $this->actingAs($this->adminUser)->delete("/campos/{$campo->id}");

        $response->assertRedirect();
        $response->assertSessionHas('error', 'No se puede eliminar el campo porque está siendo utilizado en uno o más tipos de documentos.');
        $this->assertDatabaseHas('campo_types', ['id' => $campo->id]);
    }

    /** @test */
    public function admin_can_create_date_time_types_of_campos()
    {
        foreach (['date', 'time', 'date_time'] as $type) {
            $data = [
                'name' => 'Campo ' . ucfirst($type),
                'data_type' => $type,
                'is_nullable' => true,
            ];

            $response = $this->actingAs($this->adminUser)->post('/campos', $data);

            $response->assertRedirect();
            $this->assertDatabaseHas('campo_types', ['name' => 'Campo ' . ucfirst($type), 'data_type' => $type]);
        }
    }
}
