<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Areas\AreaController;
use App\Http\Controllers\Areas\GroupController;
use App\Http\Controllers\Areas\GroupTypeController;
use App\Http\Controllers\Areas\SubgroupController;
use App\Http\Controllers\Documents\BlockController;
use App\Http\Controllers\Documents\DocumentController;
use App\Http\Controllers\DocumentTypes\CampoController;
use App\Http\Controllers\DocumentTypes\DocumentTypeController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Home\LoginController;
use App\Http\Controllers\Home\SystemController;
use App\Http\Controllers\Inbox\InboxController;
use App\Http\Controllers\Inbox\NotificationController;
use App\Http\Controllers\Storage\AndamioController;
use App\Http\Controllers\Storage\ArchivoController;
use App\Http\Controllers\Storage\BoxController;
use App\Http\Controllers\Storage\SectionController;
use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', [HomeController::class, 'index'])->name('index');

// Login routes
Route::get('/login', LoginController::class);
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Pdf routes
Route::get('/documents/pdf', [DocumentController::class, 'generatePDFReport'])->name('documents.pdfReport');
Route::get('/blocks/pdf', [BlockController::class, 'generatePDFReport'])->name('blocks.pdfReport');
Route::get('/users/pdf', [UserController::class, 'generatePDF'])->name('users.pdf');
Route::get('/activity-logs/pdf', [ActivityLogController::class, 'generatePDF'])->name('activity-logs.pdf');


// Resource routes
Route::resource('/documents', DocumentController::class);
Route::resource('/blocks', BlockController::class);
Route::resource('/document_types', DocumentTypeController::class)->except(['show']);
Route::resource('/campos', CampoController::class);
Route::resource('/users', UserController::class);
Route::resource('/areas', AreaController::class);
Route::resource('/groups', GroupController::class)->except(['index', 'show']);
Route::resource('/subgroups', SubgroupController::class)->except(['index', 'show']);
Route::resource('/group_types', GroupTypeController::class);
Route::resource('/sections', SectionController::class);
Route::resource('/andamios', AndamioController::class);
Route::resource('/boxes', BoxController::class);


Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity.logs');


Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::get('/notifications/notification/{notification}', [NotificationController::class, 'redirectAndMarkAsRead'])->name('notification.show');

Route::get('/inbox', [InboxController::class, 'index'])->name('inbox.index');
Route::put('/inbox/update-storage/{id}', [InboxController::class, 'updateBlockStorage'])->name('inbox.updateStorage');

Route::prefix('sections/{section}')->group(function () {
    // Andamios dentro de una sección
    Route::prefix('andamios')->group(function () {
        Route::get('/', [AndamioController::class, 'index'])->name('sections.andamios.index');
        Route::post('/', [AndamioController::class, 'store'])->name('sections.andamios.store');
        Route::put('/{andamio}', [AndamioController::class, 'update'])->name('sections.andamios.update');
        Route::delete('/{andamio}', [AndamioController::class, 'destroy'])->name('sections.andamios.destroy');

        // Cajas dentro de un andamio
        Route::prefix('{andamio}/boxes')->group(function () {
            Route::get('/', [BoxController::class, 'index'])->name('sections.andamios.boxes.index');
            Route::post('/', [BoxController::class, 'store'])->name('sections.andamios.boxes.store');
            Route::put('/{box}', [BoxController::class, 'update'])->name('sections.andamios.boxes.update');
            Route::delete('/{box}', [BoxController::class, 'destroy'])->name('sections.andamios.boxes.destroy');

            // Archivos dentro de una caja
            Route::prefix('{box}/archivos')->group(function () {
                Route::get('/', [ArchivoController::class, 'index'])->name('sections.andamios.boxes.archivos.index');
                Route::post('/{block}/move', [ArchivoController::class, 'moveToDefault'])->name('sections.andamios.boxes.archivos.move');
            });
        });
    });
});

Route::delete('/admin/clear-all', [SystemController::class, 'clearAll'])
    ->middleware(['auth', 'can:clear-system'])
    ->name('admin.clear_all');

Route::get('/seed-defaults', [HomeController::class, 'seedDefaults'])->name('seed.defaults');
