<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RolesControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $operatorUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('ADMINISTRADOR');

        $this->operatorUser = User::factory()->create();
        // Crear rol OPERADOR si no existe
        Role::firstOrCreate(['name' => 'OPERADOR']);
        $this->operatorUser->assignRole('OPERADOR');
    }

    /** @test */
    public function admin_can_access_roles_index()
    {
        $response = $this->actingAs($this->adminUser)->get('/roles');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('roles/index')
                ->has('roles')
                ->has('permissions')
            );
    }

    /** @test */
    public function operator_cannot_access_roles_index()
    {
        $response = $this->actingAs($this->operatorUser)->get('/roles');
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_create_a_role_with_permissions()
    {
        Permission::firstOrCreate(['name' => 'test.permission', 'guard_name' => 'web']);

        $data = [
            'name' => 'Nuevo Rol',
            'permissions' => ['test.permission']
        ];

        $response = $this->actingAs($this->adminUser)->post('/roles', $data);

        $response->assertRedirect();
        $response->assertSessionHas('message', 'Rol creado correctamente.');
        
        $role = Role::where('name', 'Nuevo Rol')->first();
        $this->assertNotNull($role);
        $this->assertTrue($role->hasPermissionTo('test.permission'));
    }

    /** @test */
    public function role_creation_fails_with_duplicate_name()
    {
        Role::create(['name' => 'Duplicado', 'guard_name' => 'web']);

        $data = ['name' => 'Duplicado'];

        $response = $this->actingAs($this->adminUser)->post('/roles', $data);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function admin_can_update_role_permissions()
    {
        $role = Role::create(['name' => 'Rol a Editar', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit.perm', 'guard_name' => 'web']);

        $data = [
            'name' => 'Rol a Editar',
            'permissions' => ['edit.perm']
        ];

        $response = $this->actingAs($this->adminUser)->put("/roles/{$role->id}", $data);

        $response->assertRedirect();
        $this->assertTrue($role->fresh()->hasPermissionTo('edit.perm'));
    }

    /** @test */
    public function admin_can_delete_a_role()
    {
        $role = Role::create(['name' => 'A Eliminar', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)->delete("/roles/{$role->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }
}
