<?php

use App\Http\Controllers\Activities\ActivityLogController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('actividades')->group(function () {
    Route::get('/', [ActivityLogController::class, 'index'])->name('activity_logs.index')->middleware('can:activity-logs.view');
    Route::get('/pdf', [ActivityLogController::class, 'pdf'])->name('activity_logs.pdf')->middleware('can:activity-logs.view');
});
