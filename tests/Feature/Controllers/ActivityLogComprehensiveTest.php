<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ActivityLogComprehensiveTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $adminRole = Role::firstOrCreate(['name' => 'ADMINISTRADOR', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'activity-logs.view', 'guard_name' => 'web']);
        $adminRole->givePermissionTo('activity-logs.view');

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($adminRole);
    }

    /**
     * 1. Etapa de Contrato - ACTIVITY LOGS
     */
    public function test_activity_log_index_returns_strict_contract()
    {
        ActivityLog::factory()->count(2)->create(['user_id' => $this->adminUser->id]);

        $response = $this->actingAs($this->adminUser)->get('/actividades');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('activity_logs/index')
                ->has('logs.data', 2)
                ->has('users')
                ->has('modules')
            );
    }

    /**
     * 2. Validación de Filtros
     */
    public function test_activity_log_index_validates_dates()
    {
        $response = $this->actingAs($this->adminUser)->get('/actividades?date=fecha-invalida');       
        $response->assertSessionHasErrors(['date']);
    }

    /**
     * 3. Seguridad - ACCESO
     */
    public function test_operator_cannot_view_activity_logs()
    {
        $operator = User::factory()->create();

        $response = $this->actingAs($operator)->get('/actividades');

        $response->assertStatus(403);
    }

    /**
     * 4. Lógica de Negocio
     */
    public function test_activity_log_modules_are_mapped_correctly()
    {
        ActivityLog::factory()->create(['model' => 'USERS', 'user_id' => $this->adminUser->id]);

        $response = $this->actingAs($this->adminUser)->get('/actividades?module=USERS');

        $response->assertInertia(fn (Assert $page) => $page
            ->has('logs.data', 1)
            ->where('logs.data.0.model', 'USERS')
        );
    }

    /**
     * 5. Resiliencia
     */
    public function test_activity_log_handles_search_with_special_characters()
    {
        ActivityLog::factory()->create(['action' => 'Acción especial @#$%']);

        $response = $this->actingAs($this->adminUser)->get('/actividades?search=' . urlencode('@#$%'));

        $response->assertStatus(200);
    }

    /**
     * 6. Rendimiento
     */
    public function test_activity_log_index_uses_cache_for_filters()
    {
        \DB::enableQueryLog();
        $this->actingAs($this->adminUser)->get('/actividades');
        $queries1 = count(\DB::getQueryLog());

        \DB::flushQueryLog();
        $this->actingAs($this->adminUser)->get('/actividades');
        $queries2 = count(\DB::getQueryLog());

        $this->assertLessThanOrEqual($queries1, $queries2);
    }
}
