<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_endpoint_requires_authentication(): void
    {
        $response = $this->get('/perfil');

        $response->assertRedirect('/login');
    }

    public function test_profile_endpoint_returns_user_data_and_permissions(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/perfil');

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('profile/show')
                ->has('user')
                ->has('permissions')
                ->has('role_names')
            );
    }
}
