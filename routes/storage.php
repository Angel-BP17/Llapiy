<?php

use App\Http\Controllers\Storage\AndamioController;
use App\Http\Controllers\Storage\ArchivoController;
use App\Http\Controllers\Storage\BoxController;
use App\Http\Controllers\Storage\SectionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('sections')->group(function () {
    Route::get('/', [SectionController::class, 'index'])->name('sections.index')->middleware('can:sections.view');
    Route::get('/report', [SectionController::class, 'report'])->name('sections.report')->middleware('can:sections.view');
    Route::post('/', [SectionController::class, 'store'])->name('sections.store')->middleware('can:sections.create');
    Route::get('/{section}', [SectionController::class, 'show'])->name('sections.show')->middleware('can:sections.view');
    Route::put('/{section}', [SectionController::class, 'update'])->name('sections.update')->middleware('can:sections.update');
    Route::delete('/{section}', [SectionController::class, 'destroy'])->name('sections.destroy')->middleware('can:sections.delete');

    Route::prefix('{section}/andamios')->group(function () {
        Route::get('/', [AndamioController::class, 'index'])->name('andamios.index')->middleware('can:andamios.view');
        Route::post('/', [AndamioController::class, 'store'])->name('andamios.store')->middleware('can:andamios.create');
        Route::put('/{andamio}', [AndamioController::class, 'update'])->name('andamios.update')->middleware('can:andamios.update');
        Route::delete('/{andamio}', [AndamioController::class, 'destroy'])->name('andamios.destroy')->middleware('can:andamios.delete');

        Route::prefix('{andamio}/boxes')->group(function () {
            Route::get('/', [BoxController::class, 'index'])->name('boxes.index')->middleware('can:boxes.view');
            Route::post('/', [BoxController::class, 'store'])->name('boxes.store')->middleware('can:boxes.create');
            Route::put('/{box}', [BoxController::class, 'update'])->name('boxes.update')->middleware('can:boxes.update');
            Route::delete('/{box}', [BoxController::class, 'destroy'])->name('boxes.delete');

            Route::prefix('{box}/archivos')->group(function () {
                Route::get('/', [ArchivoController::class, 'index'])->name('archivos.index')->middleware('can:boxes.view');
                Route::post('/{block}/move', [ArchivoController::class, 'move'])->name('archivos.move');
            });
        });
    });
});
