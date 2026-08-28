<?php

use App\Http\Controllers\Documents\BlockController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('bloques')->group(function () {
    Route::get('/', [BlockController::class, 'index'])->name('blocks.index')->middleware('can:view-blocks');
    Route::post('/', [BlockController::class, 'store'])->name('blocks.store')->middleware('can:blocks.create');
    Route::get('/pdf', [BlockController::class, 'pdf'])->name('blocks.pdf')->middleware('can:view-blocks');
    Route::get('/{block}', [BlockController::class, 'show'])->name('blocks.show')->middleware('can:view-blocks');
    Route::put('/{block}', [BlockController::class, 'update'])->name('blocks.update')->middleware('can:blocks.update');
    Route::delete('/{block}', [BlockController::class, 'destroy'])->name('blocks.destroy')->middleware('can:blocks.delete');
    Route::put('/{block}/upload', [BlockController::class, 'upload'])->name('blocks.upload')->middleware('can:blocks.upload');
    Route::get('/{block}/file', [BlockController::class, 'file'])->name('blocks.file')->middleware('can:view-blocks');
});
