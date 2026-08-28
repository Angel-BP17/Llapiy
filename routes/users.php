<?php

use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('usuarios')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index')->middleware('can:users.view');
    Route::post('/', [UserController::class, 'store'])->name('users.store')->middleware('can:users.create');
    Route::get('/pdf', [UserController::class, 'pdf'])->name('users.pdf')->middleware('can:users.view');
    Route::get('/{user}', [UserController::class, 'show'])->name('users.show')->middleware('can:users.view');
    Route::put('/{user}', [UserController::class, 'update'])->name('users.update')->middleware('can:users.update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('can:users.delete');
});
