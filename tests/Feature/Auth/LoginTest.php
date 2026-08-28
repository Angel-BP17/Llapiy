<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);
    }

    #[Test]
    public function login_screen_can_be_rendered()
    {
        $this->withoutVite();
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    #[Test]
    public function users_can_authenticate_using_the_login_screen()
    {
        $this->withoutVite();
        
        $password = 'password123';
        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        $response = $this->post('/login', [
            'user_name' => $user->user_name,
            'password' => $password,
        ]);

        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function login_requires_user_name_and_password()
    {
        $this->withoutVite();
        $response = $this->post('/login', [
            'user_name' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['user_name', 'password']);
    }

    #[Test]
    public function users_can_logout()
    {
        $this->withoutVite();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
    }
}
