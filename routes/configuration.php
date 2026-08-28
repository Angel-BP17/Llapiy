<?php

use App\Http\Controllers\Configuration\ConfigurationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:ADMINISTRADOR|COLABORADOR_DOCUMENTAL|ARCHIVO_CENTRAL'])->prefix('configuracion')->group(function () {
    Route::get('/', [ConfigurationController::class, 'index'])->name('configuration.index');
    Route::post('/theme', [ConfigurationController::class, 'updateTheme'])->name('configuration.theme.update');
});

Route::middleware(['auth', 'role:ADMINISTRADOR'])->prefix('configuracion')->group(function () {
    Route::get('/backup/export', [ConfigurationController::class, 'export'])->name('configuration.backup.export');
    Route::post('/backup/import', [ConfigurationController::class, 'import'])->name('configuration.backup.import');
});
