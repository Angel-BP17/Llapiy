<?php

namespace Tests\Feature\Controllers;

use App\Http\Controllers\Users\PermissionController;
use App\Http\Controllers\Users\RoleController;
use App\Http\Controllers\Users\UserController;
use App\Models\Area;
use App\Models\User;
use App\Services\User\UserService;
use App\Services\Users\PermissionService;
use App\Services\Users\RoleService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Mockery;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UsersControllersTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_index_returns_json(): void
    {
        $service = Mockery::mock(UserService::class);
        $service->shouldReceive('getAll')->once()->andReturn([
            'users' => User::query()->paginate(10),
            'areas' => collect(),
        ]);

        $controller = new UserController($service);
        $response = $controller->index(Mockery::mock(\App\Http\Requests\User\IndexUserRequest::class));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_user_store_returns_json_on_success(): void
    {
        $service = Mockery::mock(UserService::class);
        $request = Mockery::mock(\App\Http\Requests\User\CreateUserRequest::class);
        $created = $this->createUser(['user_name' => 'created_user']);
        $request->shouldReceive('validated')->once()->andReturn(['name' => 'John']);
        $request->shouldReceive('file')->once()->with('foto_perfil')->andReturn(null);

        $service->shouldReceive('create')->once()->with(['name' => 'John'], null)->andReturn($created);

        $controller = new UserController($service);
        $response = $controller->store($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(201, $response->status());
    }

    public function test_user_update_returns_json_on_success(): void
    {
        $service = Mockery::mock(UserService::class);
        $request = Mockery::mock(\App\Http\Requests\User\UpdateUserRequest::class);
        $user = $this->createUser();

        $service->shouldReceive('update')->once()->with($request, $user);

        $controller = new UserController($service);
        $response = $controller->update($request, $user);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_user_destroy_returns_json_on_success(): void
    {
        $service = Mockery::mock(UserService::class);
        $user = $this->createUser();

        $service->shouldReceive('delete')->once()->with($user);

        $controller = new UserController($service);
        $response = $controller->destroy($user);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_user_generate_pdf_streams_file(): void
    {
        $service = Mockery::mock(UserService::class);
        $service->shouldReceive('getUsersForDpf')->once()->andReturn(collect());

        Pdf::shouldReceive('loadView')->once()->with('users.report', Mockery::type('array'))->andReturnSelf();
        Pdf::shouldReceive('setPaper')->once()->with('a4', 'landscape')->andReturnSelf();
        Pdf::shouldReceive('stream')->once()->with('reporte_usuarios.pdf')->andReturn(response('pdf'));

        $controller = new UserController($service);
        $response = $controller->generatePDF(Request::create('/users/pdf', 'GET'));

        $this->assertSame('pdf', $response->getContent());
    }

    public function test_role_index_returns_json(): void
    {
        $service = Mockery::mock(RoleService::class);
        $service->shouldReceive('getIndexData')->once()->andReturn([
            'roles' => Role::query()->paginate(10),
            'totalRoles' => 0,
            'totalPermissions' => 0,
        ]);
        $service->shouldReceive('getPermissionGroups')->once()->andReturn([]);
        $service->shouldReceive('getPermissionLabels')->once()->andReturn([]);

        $controller = new RoleController($service);
        $response = $controller->index(Mockery::mock(\App\Http\Requests\Role\IndexRoleRequest::class));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_role_store_returns_json_on_success(): void
    {
        $service = Mockery::mock(RoleService::class);
        $request = Mockery::mock(\App\Http\Requests\Role\CreateRoleRequest::class);
        $request->shouldReceive('string')->once()->with('name')->andReturn(new Stringable('auditor'));
        $request->shouldReceive('input')->once()->with('permissions', [])->andReturn(['roles.view']);

        $role = Role::create(['name' => 'auditor']);
        $service->shouldReceive('create')->once()->with('auditor', ['roles.view'])->andReturn($role);

        $controller = new RoleController($service);
        $response = $controller->store($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(201, $response->status());
    }

    public function test_role_update_returns_json_on_success(): void
    {
        $service = Mockery::mock(RoleService::class);
        $request = Mockery::mock(\App\Http\Requests\Role\UpdateRoleRequest::class);
        $role = Role::create(['name' => 'editor']);
        $request->shouldReceive('string')->once()->with('name')->andReturn(new Stringable('editor-plus'));
        $request->shouldReceive('input')->once()->with('permissions', [])->andReturn([]);

        $service->shouldReceive('update')->once()->with($role, 'editor-plus', []);

        $controller = new RoleController($service);
        $response = $controller->update($request, $role);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_role_edit_permissions_returns_json(): void
    {
        $service = Mockery::mock(RoleService::class);
        $role = Role::create(['name' => 'reviewer']);
        $service->shouldReceive('getPermissionGroups')->once()->andReturn([]);
        $service->shouldReceive('getPermissionLabels')->once()->andReturn([]);
        $service->shouldReceive('getSelectedPermissions')->once()->with($role)->andReturn([]);

        $controller = new RoleController($service);
        $response = $controller->editPermissions($role);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_role_update_permissions_validates_and_returns_json(): void
    {
        Permission::create(['name' => 'roles.view']);
        $service = Mockery::mock(RoleService::class);
        $role = Role::create(['name' => 'manager']);
        $service->shouldReceive('updatePermissions')->once()->with($role, ['roles.view']);

        $controller = new RoleController($service);
        $request = Request::create("/roles/{$role->id}/permissions", 'PUT', [
            'permissions' => ['roles.view'],
        ]);

        $response = $controller->updatePermissions($request, $role);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_role_destroy_returns_json_on_success(): void
    {
        $service = Mockery::mock(RoleService::class);
        $role = Role::create(['name' => 'deleter']);
        $service->shouldReceive('delete')->once()->with($role);

        $controller = new RoleController($service);
        $response = $controller->destroy($role);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_permission_create_returns_method_not_allowed_json(): void
    {
        $controller = new PermissionController(Mockery::mock(PermissionService::class));
        $response = $controller->create();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(405, $response->status());
    }

    public function test_permission_store_returns_json_on_success(): void
    {
        $service = Mockery::mock(PermissionService::class);
        $request = Mockery::mock(\App\Http\Requests\Permission\CreatePermissionRequest::class);
        $request->shouldReceive('string')->once()->with('name')->andReturn(new Stringable('permissions.test'));
        $permission = Permission::create(['name' => 'permissions.test']);

        $service->shouldReceive('create')->once()->with('permissions.test')->andReturn($permission);

        $controller = new PermissionController($service);
        $response = $controller->store($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(201, $response->status());
    }

    public function test_permission_edit_returns_json(): void
    {
        $permission = Permission::create(['name' => 'permissions.editable']);
        $controller = new PermissionController(Mockery::mock(PermissionService::class));
        $response = $controller->edit($permission);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_permission_update_returns_json_on_success(): void
    {
        $service = Mockery::mock(PermissionService::class);
        $request = Mockery::mock(\App\Http\Requests\Permission\UpdatePermissionRequest::class);
        $permission = Permission::create(['name' => 'permissions.old']);
        $request->shouldReceive('string')->once()->with('name')->andReturn(new Stringable('permissions.new'));

        $service->shouldReceive('update')->once()->with($permission, 'permissions.new');

        $controller = new PermissionController($service);
        $response = $controller->update($request, $permission);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_permission_destroy_returns_json_on_success(): void
    {
        $service = Mockery::mock(PermissionService::class);
        $permission = Permission::create(['name' => 'permissions.delete-me']);
        $service->shouldReceive('delete')->once()->with($permission);

        $controller = new PermissionController($service);
        $response = $controller->destroy($permission);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    private function createUser(array $attributes = []): User
    {
        Area::query()->count();

        return User::query()->create(array_merge([
            'name' => 'User',
            'last_name' => 'Test',
            'user_name' => 'user_' . Str::lower(Str::random(8)),
            'dni' => (string) random_int(10000000, 99999999),
            'email' => Str::lower(Str::random(8)) . '@example.com',
            'password' => 'password',
        ], $attributes));
    }
}
