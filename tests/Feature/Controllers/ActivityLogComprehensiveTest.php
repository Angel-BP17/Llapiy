<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ActivityLogComprehensiveTest extends TestCase
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
     * 1. Etapa de Contrato (API Schema)
     */
    public function test_activity_log_index_returns_strict_contract()
    {
        $this->withoutExceptionHandling();
        ActivityLog::factory()->count(2)->create(['user_id' => $this->adminUser->id]);

        $response = $this->actingAs($this->adminUser)->getJson('/api/activity-logs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'logs' => [
                        'data' => [
                            '*' => ['id', 'model', 'action', 'user']
                        ]
                    ],
                    'users', 'modules'
                ]
            ]);
    }

    /**
     * 2. Etapa de Validación (Gatekeeping)
     */
    public function test_activity_log_index_validates_dates()
    {
        $response = $this->actingAs($this->adminUser)->getJson('/api/activity-logs?date=fecha-invalida');
        $response->assertStatus(422);
    }

    /**
     * 3. Etapa de Seguridad (RBAC)
     */
    public function test_operator_cannot_view_activity_logs()
    {
        $operator = User::factory()->create();
        $response = $this->actingAs($operator)->getJson('/api/activity-logs');
        $response->assertStatus(403);
    }

    /**
     * 5. Etapa de Reglas de Negocio
     * Los módulos deben estar simplificados (sin App\Models\).
     */
    public function test_activity_log_modules_are_mapped_correctly()
    {
        ActivityLog::factory()->create(['model' => 'App\\Models\\Document']);

        $response = $this->actingAs($this->adminUser)->getJson('/api/activity-logs');

        $response->assertStatus(200);
        $modules = $response->json('data.modules');
        $this->assertContains('Document', $modules);
    }

    /**
     * 7. Etapa de Resiliencia
     */
    public function test_activity_log_handles_search_with_special_characters()
    {
        ActivityLog::factory()->create(['model' => 'App\\Models\\ÁreaEspecial']);
        
        $response = $this->actingAs($this->adminUser)->getJson('/api/activity-logs?module=ÁreaEspecial');
        $response->assertStatus(200);
    }

    /**
     * 8. Etapa de Rendimiento
     */
    public function test_activity_log_index_uses_cache_for_filters()
    {
        ActivityLog::factory()->create(['user_id' => $this->adminUser->id]);

        // Primera llamada puebla el cache
        $this->actingAs($this->adminUser)->getJson('/api/activity-logs');
        
        $this->assertTrue(\Illuminate\Support\Facades\Cache::has('activity_users_list'));
        $this->assertTrue(\Illuminate\Support\Facades\Cache::has('activity_modules_list'));
    }
}
