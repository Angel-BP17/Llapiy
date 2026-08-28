<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\DocumentType;
use App\Models\CampoType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;

class DocumentTypesControllersTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $adminRole = Role::firstOrCreate(['name' => 'ADMINISTRADOR', 'guard_name' => 'web']);
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($adminRole);
    }

    /**
     * TIPOS DE DOCUMENTOS
     */
    public function test_document_type_index_returns_strict_contract()
    {
        DocumentType::factory()->count(2)->create();

        $response = $this->actingAs($this->adminUser)->get('/tipos-documentos');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('document_types/index')
                ->has('documentTypes')
            );
    }

    public function test_document_type_store_redirects_on_success()
    {
        $data = [
            'name' => 'Nuevo Tipo Doc',
            'campos' => json_encode([]),
            'groups' => json_encode([]),
            'subgroups' => json_encode([])
        ];

        $response = $this->actingAs($this->adminUser)->post('/tipos-documentos', $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('document_types', ['name' => 'Nuevo Tipo Doc']);
    }

    /**
     * CAMPOS
     */
    public function test_campo_index_returns_strict_contract()
    {
        CampoType::factory()->count(2)->create();

        $response = $this->actingAs($this->adminUser)->get('/campos');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('campos/index')
                ->has('campos')
            );
    }

    public function test_campo_store_redirects_on_success()
    {
        $data = [
            'name' => 'Nuevo Campo',
            'type' => 'string'
        ];

        $response = $this->actingAs($this->adminUser)->post('/campos', $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('campo_types', ['name' => 'Nuevo Campo']);
    }
}
