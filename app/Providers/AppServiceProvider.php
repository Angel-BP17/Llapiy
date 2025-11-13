<?php

namespace App\Providers;


use Illuminate\Database\Eloquent\Model;
use App\Observers\ActivityObserver;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('es');
        foreach (glob(app_path('Models') . '/*.php') as $modelFile) {
            $modelClass = 'App\\Models\\' . pathinfo($modelFile, PATHINFO_FILENAME);
            if (class_exists($modelClass) && is_subclass_of($modelClass, Model::class)) {
                $modelClass::observe(ActivityObserver::class);
            }
        }
    }
}
