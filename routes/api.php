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
use App\Http\Controllers\Users\RoleController;
use App\Http\Controllers\Users\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/auth/login', [LoginController::class, 'loginApi'])->name('auth.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [LoginController::class, 'logout'])->name('auth.logout');

    Route::get('/user', function (Request $request) {
        return response()->json(['data' => $request->user()]);
    })->name('auth.user');

    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard.index');

    Route::put('/documents/{document}/upload', [DocumentController::class, 'uploadFile'])->name('documents.upload');
    Route::put('/blocks/{block}/upload', [BlockController::class, 'uploadFile'])->name('blocks.upload');

    Route::apiResource('documents', DocumentController::class)->except(['show']);
    Route::apiResource('blocks', BlockController::class)->except(['show']);
    Route::apiResource('document_types', DocumentTypeController::class)->except(['show']);
    Route::apiResource('campos', CampoController::class)->except(['show']);
    Route::apiResource('users', UserController::class)->except(['show']);
    Route::apiResource('roles', RoleController::class)->except(['show']);
    Route::get('/roles/{role}/permissions', [RoleController::class, 'editPermissions'])->name('roles.permissions.show');
    Route::put('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
    Route::apiResource('areas', AreaController::class)->except(['create', 'edit']);
    Route::apiResource('group_types', GroupTypeController::class)->except(['show']);
    Route::apiResource('sections', SectionController::class)->except(['show']);

    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/{group}', [GroupController::class, 'edit'])->name('groups.show');
    Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update');
    Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');

    Route::post('/subgroups', [SubgroupController::class, 'store'])->name('subgroups.store');
    Route::get('/subgroups/{subgroup}', [SubgroupController::class, 'edit'])->name('subgroups.show');
    Route::put('/subgroups/{subgroup}', [SubgroupController::class, 'update'])->name('subgroups.update');
    Route::delete('/subgroups/{subgroup}', [SubgroupController::class, 'destroy'])->name('subgroups.destroy');

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity.logs');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}', [NotificationController::class, 'redirectAndMarkAsRead'])->name('notification.show');

    Route::get('/inbox', [InboxController::class, 'index'])->name('inbox.index');
    Route::put('/inbox/update-storage/{id}', [InboxController::class, 'updateBlockStorage'])->name('inbox.updateStorage');

    Route::prefix('sections/{section}')->group(function () {
        Route::prefix('andamios')->group(function () {
            Route::get('/', [AndamioController::class, 'index'])->name('sections.andamios.index');
            Route::post('/', [AndamioController::class, 'store'])->name('sections.andamios.store');
            Route::put('/{andamio}', [AndamioController::class, 'update'])->name('sections.andamios.update');
            Route::delete('/{andamio}', [AndamioController::class, 'destroy'])->name('sections.andamios.destroy');

            Route::prefix('{andamio}/boxes')->group(function () {
                Route::get('/', [BoxController::class, 'index'])->name('sections.andamios.boxes.index');
                Route::post('/', [BoxController::class, 'store'])->name('sections.andamios.boxes.store');
                Route::put('/{box}', [BoxController::class, 'update'])->name('sections.andamios.boxes.update');
                Route::delete('/{box}', [BoxController::class, 'destroy'])->name('sections.andamios.boxes.destroy');

                Route::prefix('{box}/archivos')->group(function () {
                    Route::get('/', [ArchivoController::class, 'index'])->name('sections.andamios.boxes.archivos.index');
                    Route::post('/{block}/move', [ArchivoController::class, 'moveToDefault'])->name('sections.andamios.boxes.archivos.move');
                });
            });
        });
    });

    Route::delete('/admin/clear-all', [SystemController::class, 'clearAll'])
        ->middleware('can:clear-system')
        ->name('admin.clear_all');

    Route::get('/storage-link', function () {
        $exitCode = Artisan::call('storage:link');

        return response()->json([
            'ok' => $exitCode === 0,
            'exit_code' => $exitCode,
            'output' => Artisan::output(),
        ]);
    })->name('storage.link');
});
