<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

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
