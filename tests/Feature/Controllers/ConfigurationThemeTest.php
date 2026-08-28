<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfigurationThemeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('ADMINISTRADOR');
    }

    /** @test */
    public function admin_can_update_their_theme_setting()
    {
        $response = $this->actingAs($this->admin)->post('/configuracion/theme', [
            'theme' => 'dark'
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('settings', [
            'user_id' => $this->admin->id,
            'key' => 'theme',
            'value' => 'dark'
        ]);

        // Toggle back to light
        $response = $this->actingAs($this->admin)->post('/configuracion/theme', [
            'theme' => 'light'
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('settings', [
            'user_id' => $this->admin->id,
            'key' => 'theme',
            'value' => 'light'
        ]);
    }

    /** @test */
    public function theme_validation_requires_valid_theme()
    {
        $response = $this->actingAs($this->admin)->post('/configuracion/theme', [
            'theme' => 'invalid-theme'
        ]);

        $response->assertSessionHasErrors(['theme']);
    }
}
