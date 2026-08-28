<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Area;
use App\Models\Group;
use App\Models\GroupType;
use App\Models\AreaGroupType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class UsersComprehensiveTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $adminRole = Role::firstOrCreate(['name' => 'ADMINISTRADOR', 'guard_name' => 'web']);
        
        // Crear permisos necesarios para el nuevo sistema
        Permission::firstOrCreate(['name' => 'users.view', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'users.create', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'users.update', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'users.delete', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'roles.view', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'roles.create', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'roles.update', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'roles.delete', 'guard_name' => 'web']);

        $adminRole->givePermissionTo(Permission::all());

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($adminRole);
    }

    /**
     * 1. Etapa de Contrato (Inertia Props) - USUARIOS
     */
    public function test_user_index_returns_strict_contract()
    {
        User::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser)->get('/usuarios');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('users/index')
                ->has('users')
                ->has('areas')
                ->has('roles')
                ->has('stats')
            );
    }

    /**
     * 2. Etapa de Validación (Gatekeeping) - ROLES
     */
    public function test_role_creation_fails_with_duplicate_name()
    {
        Role::create(['name' => 'EXISTENTE', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)->post('/roles', [
            'name' => 'EXISTENTE'
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * 3. Etapa de Seguridad (RBAC) - PERMISOS
     */
    public function test_non_admin_cannot_access_user_list()
    {
        $operator = User::factory()->create();
        // No tiene el rol ADMINISTRADOR ni el permiso users.view

        $response = $this->actingAs($operator)->get('/usuarios');

        $response->assertStatus(403);
    }

    /**
     * 4. Etapa de Integridad - USUARIOS Y ÁREAS
     */
    public function test_user_cannot_be_assigned_to_non_existent_group()
    {
        $response = $this->actingAs($this->adminUser)->post('/usuarios', [
            'name' => 'Test',
            'last_name' => 'User',
            'user_name' => 'testuser',
            'dni' => '12345678',
            'email' => 'test@example.com',
            'password' => 'password123',
            'group_id' => 99999 // ID inexistente
        ]);

        $response->assertSessionHasErrors(['group_id']);
    }

    /**
     * 6. Etapa de Manejo de Archivos - FOTO PERFIL
     */
    public function test_user_can_upload_profile_photo()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('avatar.jpg');
        $area = Area::factory()->create();
        $gt = GroupType::factory()->create();
        $agt = AreaGroupType::create(['area_id' => $area->id, 'group_type_id' => $gt->id]);
        $group = Group::factory()->create(['area_group_type_id' => $agt->id]);

        $role = Role::firstOrCreate(['name' => 'OPERADOR', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)->post('/usuarios', [
            'name' => 'Photo',
            'last_name' => 'User',
            'user_name' => 'photouser',
            'dni' => '87654321',
            'email' => 'photo@example.com',
            'password' => 'password123',
            'foto_perfil' => $file,
            'role_id' => $role->id,
            'group_id' => $group->id
        ]);

        $response->assertRedirect();
        $user = User::where('user_name', 'photouser')->first();
        $this->assertNotNull($user->foto_perfil);
        Storage::disk('public')->assertExists($user->foto_perfil);
    }

    /**
     * 5. Etapa de Reglas de Negocio
     * Validar que la asignación de roles funcione correctamente tras la creación.
     */
    public function test_user_creation_assigns_roles_correctly()
    {
        $role = Role::firstOrCreate(['name' => 'OPERADOR', 'guard_name' => 'web']);
        $area = Area::factory()->create();
        $gt = GroupType::factory()->create();
        $agt = AreaGroupType::create(['area_id' => $area->id, 'group_type_id' => $gt->id]);
        $group = Group::factory()->create(['area_group_type_id' => $agt->id]);

        $response = $this->actingAs($this->adminUser)->post('/usuarios', [
            'name' => 'RoleTest',
            'last_name' => 'User',
            'user_name' => 'roletester',
            'dni' => '99887766',
            'email' => 'roles@test.com',
            'password' => 'password123',
            'group_id' => $group->id,
            'roles' => ['OPERADOR']
        ]);

        $response->assertRedirect();
        $user = User::where('user_name', 'roletester')->first();
        $this->assertTrue($user->hasRole('OPERADOR'));
    }

    /**
     * 7. Etapa de Resiliencia
     * Nombres con caracteres especiales y búsqueda difusa.
     */
    public function test_user_search_handles_complex_names()
    {
        User::factory()->create(['name' => 'André', 'last_name' => 'Muñoz Ñandú']);
        
        $response = $this->actingAs($this->adminUser)->get('/usuarios?search=' . urlencode('Muñoz Ñ'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->has('users', 1)
        );
    }

    /**
     * 8. Etapa de Rendimiento - ROLES Y PERMISOS
     */
    public function test_role_index_is_optimized()
    {
        for ($i = 0; $i < 5; $i++) {
            Role::create(['name' => "Role_{$i}", 'guard_name' => 'web']);
        }
        
        \DB::enableQueryLog();
        $this->actingAs($this->adminUser)->get('/roles');
        $queries = \DB::getQueryLog();
        \DB::disableQueryLog();

        // No debería haber consultas N+1 para cargar los permisos de cada rol
        $this->assertLessThan(20, count($queries));
    }
}
