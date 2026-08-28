<?php

use App\Http\Controllers\Areas\AreaController;
use App\Http\Controllers\Areas\GroupController;
use App\Http\Controllers\Areas\GroupTypeController;
use App\Http\Controllers\Areas\SubgroupController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Areas
    Route::prefix('areas')->group(function () {
        Route::get('/', [AreaController::class, 'index'])->name('areas.index')->middleware('can:areas.view');
        Route::post('/', [AreaController::class, 'store'])->name('areas.store')->middleware('can:areas.create');
        Route::get('/{area}', [AreaController::class, 'show'])->name('areas.show')->middleware('can:areas.view');
        Route::put('/{area}', [AreaController::class, 'update'])->name('areas.update')->middleware('can:areas.update');
        Route::delete('/{area}', [AreaController::class, 'destroy'])->name('areas.destroy')->middleware('can:areas.delete');
    });

    // Group Types
    Route::prefix('tipos-grupos')->group(function () {
        Route::get('/', [GroupTypeController::class, 'index'])->name('group_types.index');
        Route::post('/', [GroupTypeController::class, 'store'])->name('group_types.store');
        Route::put('/{id}', [GroupTypeController::class, 'update'])->name('group_types.update');
        Route::delete('/{id}', [GroupTypeController::class, 'destroy'])->name('group_types.destroy');
    });

    // Groups
    Route::prefix('groups')->group(function () {
        Route::post('/', [GroupController::class, 'store'])->name('groups.store')->middleware('can:groups.create');
        Route::put('/{id}', [GroupController::class, 'update'])->name('groups.update')->middleware('can:groups.update');
        Route::delete('/{id}', [GroupController::class, 'destroy'])->name('groups.destroy')->middleware('can:groups.delete');
    });

    // Subgroups
    Route::prefix('subgroups')->group(function () {
        Route::post('/', [SubgroupController::class, 'store'])->name('subgroups.store')->middleware('can:subgroups.create');
        Route::put('/{id}', [SubgroupController::class, 'update'])->name('subgroups.update')->middleware('can:subgroups.update');
        Route::delete('/{id}', [SubgroupController::class, 'destroy'])->name('subgroups.destroy')->middleware('can:subgroups.delete');
    });
});
