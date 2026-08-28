<?php

namespace Tests\Unit\Services\Users;

use App\Services\Users\RoleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RoleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_create_uses_permission_guard_even_if_default_guard_is_sanctum(): void
    {
        config(['auth.defaults.guard' => 'sanctum']);
        Permission::create(['name' => 'andamios.create', 'guard_name' => 'web']);

        $service = app(RoleService::class);
        $role = $service->create('encargado', ['andamios.create']);

        $this->assertSame('web', $role->fresh()->guard_name);
        $this->assertSame(['andamios.create'], $role->permissions()->pluck('name')->all());
    }

    public function test_update_permissions_moves_role_guard_to_permission_guard(): void
    {
        config(['auth.defaults.guard' => 'sanctum']);
        Permission::create(['name' => 'andamios.create', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'encargado', 'guard_name' => 'sanctum']);

        $service = app(RoleService::class);
        $service->updatePermissions($role, ['andamios.create']);

        $this->assertSame('web', $role->fresh()->guard_name);
        $this->assertSame(['andamios.create'], $role->permissions()->pluck('name')->all());
    }
}
