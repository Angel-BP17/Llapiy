<?php

use App\Http\Controllers\Home\AuthController;
use App\Http\Controllers\Home\DashboardController;
use App\Http\Controllers\Home\SystemController;
use Illuminate\Support\Facades\Route;

// Rutas de Invitados
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

// Rutas Autenticadas
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
    
    Route::get('/perfil', [\App\Http\Controllers\Users\ProfileController::class, 'show'])->name('profile');
    Route::get('/notifications/api', [\App\Http\Controllers\Home\DashboardController::class, 'notifications'])->name('notifications.api');

    Route::delete('/admin/clear-all', [SystemController::class, 'clearAll'])
        ->middleware('can:clear-system')
        ->name('admin.clear_all');
});
