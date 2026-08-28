<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Area;
use App\Models\Group;
use App\Models\GroupType;
use App\Models\AreaGroupType;
use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class ModularControllersTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $adminRole = Role::firstOrCreate(['name' => 'ADMINISTRADOR', 'guard_name' => 'web']);
        
        // Crear permisos necesarios
        Permission::firstOrCreate(['name' => 'users.view', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'documents.view', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(['users.view', 'documents.view']);

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($adminRole);
    }

    /** @test */
    public function users_index_controller_works_and_returns_inertia_page()
    {
        User::factory()->count(5)->create();

        $response = $this->actingAs($this->adminUser)->get('/usuarios');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('users/index')
            ->has('users')
            ->has('roles')
            ->has('areas')
        );
    }

    /** @test */
    public function documents_index_controller_works_and_returns_inertia_page()
    {
        $response = $this->actingAs($this->adminUser)->get('/documentos');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('documents/index')
            ->has('documents')
            ->has('documentTypes')
        );
    }

    /** @test */
    public function inbox_index_controller_works_and_returns_inertia_page()
    {
        Permission::firstOrCreate(['name' => 'inbox.view', 'guard_name' => 'web']);
        $this->adminUser->givePermissionTo('inbox.view');

        $response = $this->actingAs($this->adminUser)->get('/bandeja');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('inbox/index')
            ->has('documents')
        );
    }

    /** @test */
    public function roles_index_controller_works_and_returns_inertia_page()
    {
        Permission::firstOrCreate(['name' => 'roles.view', 'guard_name' => 'web']);
        $this->adminUser->givePermissionTo('roles.view');

        $response = $this->actingAs($this->adminUser)->get('/roles');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('roles/index')
            ->has('roles')
            ->has('permissions')
        );
    }

    /** @test */
    public function storage_sections_index_controller_works_and_returns_inertia_page()
    {
        Permission::firstOrCreate(['name' => 'sections.view', 'guard_name' => 'web']);
        $this->adminUser->givePermissionTo('sections.view');

        $response = $this->actingAs($this->adminUser)->get('/sections');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('storage/index')
            ->has('sections')
            ->where('level', 'sections')
        );
    }

    /** @test */
    public function areas_index_controller_works_and_returns_inertia_page()
    {
        $response = $this->actingAs($this->adminUser)->get('/areas');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('areas/index')
            ->has('areas')
        );
    }
}
