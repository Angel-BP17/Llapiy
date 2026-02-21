<?php

namespace Tests\Feature\Controllers;

use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Home\LoginController;
use App\Http\Controllers\Home\SystemController;
use App\Services\Home\HomeService;
use App\Services\Home\LoginService;
use App\Services\Home\SystemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Mockery;
use Tests\TestCase;

class HomeControllersTest extends TestCase
{
    public function test_home_index_returns_json_payload(): void
    {
        $service = Mockery::mock(HomeService::class);
        $service->shouldReceive('getDashboardData')->once()->andReturn(['foo' => 'bar']);

        $controller = new HomeController($service);
        $response = $controller->index(Mockery::mock(\App\Http\Requests\Home\IndexHomeRequest::class));

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
        $this->assertSame('Dashboard obtenido correctamente.', $response->getData(true)['message']);
    }

    public function test_home_seed_defaults_returns_json_on_success(): void
    {
        $service = Mockery::mock(HomeService::class);
        $service->shouldReceive('seedDefaults')->once();

        $controller = new HomeController($service);
        $response = $controller->seedDefaults();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_home_seed_defaults_returns_json_on_failure(): void
    {
        $service = Mockery::mock(HomeService::class);
        $service->shouldReceive('seedDefaults')->once()->andThrow(new \RuntimeException('boom'));

        $controller = new HomeController($service);
        $response = $controller->seedDefaults();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(500, $response->status());
    }

    public function test_login_invoke_returns_login_view(): void
    {
        $controller = new LoginController(Mockery::mock(LoginService::class));
        $response = $controller();

        $this->assertInstanceOf(View::class, $response);
        $this->assertSame('login', $response->name());
    }

    public function test_login_redirects_to_home_on_success(): void
    {
        $service = Mockery::mock(LoginService::class);
        $service->shouldReceive('attempt')->once()->andReturn(true);

        $controller = new LoginController($service);
        $request = Request::create('/login', 'POST', [
            'user_name' => 'tester',
            'password' => 'secret',
        ]);

        $response = $controller->login($request);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(url('/'), $response->getTargetUrl());
    }

    public function test_login_returns_errors_on_invalid_credentials(): void
    {
        $service = Mockery::mock(LoginService::class);
        $service->shouldReceive('attempt')->once()->andReturn(false);

        $controller = new LoginController($service);
        $request = Request::create('/login', 'POST', [
            'user_name' => 'tester',
            'password' => 'wrong',
        ]);

        $response = $controller->login($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(302, $response->getStatusCode());
    }

    public function test_logout_calls_service_and_returns_json(): void
    {
        $service = Mockery::mock(LoginService::class);
        $service->shouldReceive('logout')->once();

        $controller = new LoginController($service);
        $request = Request::create('/api/auth/logout', 'POST');

        $response = $controller->logout($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_login_api_returns_token_payload_on_success(): void
    {
        $service = Mockery::mock(LoginService::class);
        $service->shouldReceive('attempt')->once()->andReturn(true);

        $tokenContainer = new class {
            public string $plainTextToken = 'token-123';
        };

        $user = new class($tokenContainer) {
            public function __construct(private readonly object $tokenContainer)
            {
            }

            public function createToken(string $name): object
            {
                return $this->tokenContainer;
            }

            public function toArray(): array
            {
                return ['id' => 1, 'user_name' => 'tester'];
            }
        };

        Auth::shouldReceive('user')->once()->andReturn($user);

        $controller = new LoginController($service);
        $request = Request::create('/api/auth/login', 'POST', [
            'user_name' => 'tester',
            'password' => 'secret',
        ]);

        $response = $controller->loginApi($request);
        $data = $response->getData(true);

        $this->assertSame(200, $response->status());
        $this->assertSame('token-123', $data['data']['token']);
    }

    public function test_login_api_returns_unauthorized_on_failure(): void
    {
        $service = Mockery::mock(LoginService::class);
        $service->shouldReceive('attempt')->once()->andReturn(false);

        $controller = new LoginController($service);
        $request = Request::create('/api/auth/login', 'POST', [
            'user_name' => 'tester',
            'password' => 'bad',
        ]);

        $response = $controller->loginApi($request);
        $this->assertSame(401, $response->status());
    }

    public function test_system_clear_all_returns_json_with_success(): void
    {
        $service = Mockery::mock(SystemService::class);
        $service->shouldReceive('clearAll')->once();

        $controller = new SystemController($service);
        $response = $controller->clearAll();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->status());
    }

    public function test_system_clear_all_returns_json_with_error_when_exception_occurs(): void
    {
        $service = Mockery::mock(SystemService::class);
        $service->shouldReceive('clearAll')->once()->andThrow(new \Exception('fail'));

        $controller = new SystemController($service);
        $response = $controller->clearAll();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(500, $response->status());
    }
}
