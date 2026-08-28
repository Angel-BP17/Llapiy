<?php

namespace App\Providers;

use Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, string $ability) {
            return $user->hasRole('ADMINISTRADOR') ? true : null;
        });

        // Gates unificadas para visualización (Capa de Ruta)
        Gate::define('view-documents', function ($user) {
            return $user->can('documents.view.all') ||
                $user->can('documents.view.group') ||
                $user->can('documents.view.own');
        });

        Gate::define('view-blocks', function ($user) {
            return $user->can('blocks.view.all') ||
                $user->can('blocks.view.group') ||
                $user->can('blocks.view.own');
        });
    }
}
