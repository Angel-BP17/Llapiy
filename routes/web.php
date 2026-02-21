<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Documents\BlockController;
use App\Http\Controllers\Documents\DocumentController;
use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Only PDF report endpoints and API documentation live in web routes.
|
*/

Route::redirect('/', '/docs')->name('home.redirect');
Route::redirect('/docs', '/doc')->name('docs.redirect');
Route::view('/doc', 'doc')->name('docs.view');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/documents/pdf', [DocumentController::class, 'generatePDFReport'])->name('documents.pdfReport');
    Route::get('/blocks/pdf', [BlockController::class, 'generatePDFReport'])->name('blocks.pdfReport');
    Route::get('/users/pdf', [UserController::class, 'generatePDF'])->name('users.pdf');
    Route::get('/activity-logs/pdf', [ActivityLogController::class, 'generatePDF'])->name('activity-logs.pdf');
});
