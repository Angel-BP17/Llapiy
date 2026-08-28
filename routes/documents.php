<?php

use App\Http\Controllers\Documents\DocumentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('documentos')->group(function () {
    Route::get('/', [DocumentController::class, 'index'])->name('documents.index')->middleware('can:view-documents');
    Route::post('/', [DocumentController::class, 'store'])->name('documents.store')->middleware('can:documents.create');
    Route::get('/pdf', [DocumentController::class, 'pdf'])->name('documents.pdf')->middleware('can:view-documents');
    Route::get('/{document}', [DocumentController::class, 'show'])->name('documents.show')->middleware('can:view-documents');
    Route::put('/{document}', [DocumentController::class, 'update'])->name('documents.update')->middleware('can:documents.update');
    Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy')->middleware('can:documents.delete');
    Route::get('/{document}/file', [DocumentController::class, 'file'])->name('documents.file')->middleware('can:view-documents');
    Route::put('/{document}/upload', [DocumentController::class, 'upload'])->name('documents.upload')->middleware('can:documents.upload');
});
