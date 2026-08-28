<?php

use App\Http\Controllers\DocumentTypes\CampoController;
use App\Http\Controllers\DocumentTypes\DocumentTypeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::prefix('tipos-documentos')->group(function () {
        Route::get('/', [DocumentTypeController::class, 'index'])->name('document_types.index');
        Route::post('/', [DocumentTypeController::class, 'store'])->name('document_types.store');
        Route::get('/{documentType}', [DocumentTypeController::class, 'show'])->name('document_types.show');
        Route::put('/{documentType}', [DocumentTypeController::class, 'update'])->name('document_types.update');
        Route::delete('/{documentType}', [DocumentTypeController::class, 'destroy'])->name('document_types.destroy');
    });

    Route::prefix('campos')->group(function () {
        Route::get('/', [CampoController::class, 'index'])->name('campos.index')->middleware('can:campos.view');
        Route::post('/', [CampoController::class, 'store'])->name('campos.store')->middleware('can:campos.create');
        Route::get('/{campo}', [CampoController::class, 'show'])->name('campos.show')->middleware('can:campos.view');
        Route::put('/{campo}', [CampoController::class, 'update'])->name('campos.update')->middleware('can:campos.update');
        Route::delete('/{campo}', [CampoController::class, 'destroy'])->name('campos.destroy')->middleware('can:campos.delete');
    });
});
