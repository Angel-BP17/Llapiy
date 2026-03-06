<?php

namespace Tests\Feature\Requests;

use App\Models\User;
use App\Models\Area;
use App\Models\Group;
use App\Models\GroupType;
use App\Models\AreaGroupType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserRequestValidationTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    protected function setUp(): void
    {
        parent::setUp();

        // Creamos la estructura mínima necesaria dinámicamente
        $area = Area::create(['descripcion' => 'Area Test', 'abreviacion' => 'AT']);
        $groupType = GroupType::create(['descripcion' => 'Tipo Test', 'abreviacion' => 'TT']);
        $areaGroupType = AreaGroupType::create([
            'area_id' => $area->id,
            'group_type_id' => $groupType->id
        ]);
        $this->group = Group::create([
            'area_group_type_id' => $areaGroupType->id,
            'descripcion' => 'Grupo Test',
            'abreviacion' => 'GT'
        ]);

        Permission::findOrCreate('users.create', 'web');
        $this->role = Role::findOrCreate('ADMINISTRADOR', 'web');

        Route::put('/test-user-update/{user}', function (\App\Http\Requests\User\UpdateUserRequest $request, User $user) {
            return response()->json(['validated' => $request->validated(), 'all' => $request->all()]);
        });
        
        Route::post('/test-user-create', function (\App\Http\Requests\User\CreateUserRequest $request) {
            return response()->json(['validated' => $request->validated(), 'all' => $request->all()]);
        });
    }

    public function test_user_request_transforms_roles_to_uppercase(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole($this->role);
        $this->actingAs($admin);

        $response = $this->postJson('/test-user-create', [
            'name' => 'Test',
            'last_name' => 'User',
            'user_name' => 'test_user_unique_' . time(),
            'dni' => '99999999',
            'email' => 'test' . time() . '@example.com',
            'password' => 'password123',
            'roles' => ['administrador'], // Enviado en minúsculas
            'group_id' => $this->group->id
        ]);

        $response->assertStatus(200);
        $this->assertEquals(['ADMINISTRADOR'], $response->json('all.roles'));
    }
}
