<?php

use App\Http\Controllers\Documents\DocumentarySeriesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('series-documentales')->group(function () {
    Route::get('/', [DocumentarySeriesController::class, 'index'])
        ->name('documentary_series.index')
        ->middleware('can:documentary-series.view');

    Route::post('/', [DocumentarySeriesController::class, 'store'])
        ->name('documentary_series.store')
        ->middleware('can:documentary-series.create');

    Route::put('/{documentary_series}', [DocumentarySeriesController::class, 'update'])
        ->name('documentary_series.update')
        ->middleware('can:documentary-series.update');

    Route::delete('/{documentary_series}', [DocumentarySeriesController::class, 'destroy'])
        ->name('documentary_series.destroy')
        ->middleware('can:documentary-series.delete');
});
