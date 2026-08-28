<?php

use App\Http\Controllers\Users\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('roles')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('roles.index')->middleware('can:roles.view');
    Route::post('/', [RoleController::class, 'store'])->name('roles.store')->middleware('can:roles.create');
    Route::put('/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('can:roles.update');
    Route::delete('/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('can:roles.delete');
    Route::put('/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
});
