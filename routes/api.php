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
use App\Http\Controllers\Users\ProfileController;
use App\Http\Controllers\Users\RoleController;
use App\Http\Controllers\Users\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\StatsController;

Route::post('/auth/login', [LoginController::class, 'login'])->name('auth.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard/stats', [StatsController::class, 'getDashboardStats']);
    Route::post('/auth/logout', [LoginController::class, 'logout'])->name('auth.logout');
    Route::get('/profile', ProfileController::class)->name('auth.profile');
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard.index');

    // DOCUMENTOS
    Route::group(['prefix' => 'documents'], function () {
        Route::get('/', [DocumentController::class, 'index'])->middleware('can:view-documents');
        Route::post('/', [DocumentController::class, 'store'])->middleware('can:documents.create');
        Route::get('/{document}', [DocumentController::class, 'show'])->middleware('can:view-documents');
        Route::put('/{document}', [DocumentController::class, 'update'])->middleware('can:documents.update');
        Route::delete('/{document}', [DocumentController::class, 'destroy'])->middleware('can:documents.delete');
        Route::get('/{document}/file', [DocumentController::class, 'viewFile'])->middleware('can:view-documents');
        Route::put('/{document}/upload', [DocumentController::class, 'uploadFile'])->middleware('can:documents.upload');
    });

    // BLOQUES
    Route::group(['prefix' => 'blocks'], function () {
        Route::get('/', [BlockController::class, 'index'])->middleware('can:view-blocks');
        Route::post('/', [BlockController::class, 'store'])->middleware('can:blocks.create');
        Route::get('/{block}', [BlockController::class, 'show'])->middleware('can:view-blocks');
        Route::put('/{block}', [BlockController::class, 'update'])->middleware('can:blocks.update');
        Route::delete('/{block}', [BlockController::class, 'destroy'])->middleware('can:blocks.delete');
        Route::get('/{block}/file', [BlockController::class, 'viewFile'])->middleware('can:view-blocks');
        Route::put('/{block}/upload', [BlockController::class, 'uploadFile'])->middleware('can:blocks.upload');
    });

    // TIPOS DE DOCUMENTO Y CAMPOS
    Route::apiResource('document_types', DocumentTypeController::class);
    Route::apiResource('campos', CampoController::class);

    // USUARIOS
    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [UserController::class, 'index'])->middleware('can:users.view');
        Route::post('/', [UserController::class, 'store'])->middleware('can:users.create');
        Route::get('/{user}', [UserController::class, 'show'])->middleware('can:users.view');
        Route::put('/{user}', [UserController::class, 'update'])->middleware('can:users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('can:users.delete');
        Route::get('/report/pdf', [UserController::class, 'generatePDF'])->middleware('can:users.view');
    });

    // ROLES Y PERMISOS
    Route::group(['prefix' => 'roles'], function () {
        Route::get('/', [RoleController::class, 'index'])->middleware('can:roles.view');
        Route::post('/', [RoleController::class, 'store'])->middleware('can:roles.create');
        Route::get('/{role}', [RoleController::class, 'show'])->middleware('can:roles.view');
        Route::put('/{role}', [RoleController::class, 'update'])->middleware('can:roles.update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->middleware('can:roles.delete');
        Route::get('/{role}/permissions', [RoleController::class, 'editPermissions'])->middleware('can:roles.update');
        Route::put('/{role}/permissions', [RoleController::class, 'updatePermissions'])->middleware('can:roles.update');
    });

    // AREAS Y GRUPOS
    Route::group(['prefix' => 'areas'], function () {
        Route::get('/', [AreaController::class, 'index'])->middleware('can:areas.view');
        Route::post('/', [AreaController::class, 'store'])->middleware('can:areas.create');
        Route::get('/{area}', [AreaController::class, 'show'])->middleware('can:areas.view');
        Route::put('/{area}', [AreaController::class, 'update'])->middleware('can:areas.update');
        Route::delete('/{area}', [AreaController::class, 'destroy'])->middleware('can:areas.delete');
    });

    Route::apiResource('group_types', GroupTypeController::class);

    Route::post('/groups', [GroupController::class, 'store'])->middleware('can:groups.create');
    Route::get('/groups/{group}', [GroupController::class, 'edit'])->middleware('can:groups.view');
    Route::put('/groups/{group}', [GroupController::class, 'update'])->middleware('can:groups.update');
    Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->middleware('can:groups.delete');

    Route::post('/subgroups', [SubgroupController::class, 'store'])->middleware('can:subgroups.create');
    Route::get('/subgroups/{subgroup}', [SubgroupController::class, 'edit'])->middleware('can:subgroups.view');
    Route::put('/subgroups/{subgroup}', [SubgroupController::class, 'update'])->middleware('can:subgroups.update');
    Route::delete('/subgroups/{subgroup}', [SubgroupController::class, 'destroy'])->middleware('can:subgroups.delete');

    // OTROS SERVICIOS
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->middleware('can:activity-logs.view');
    Route::get('/notifications', [NotificationController::class, 'index'])->middleware('can:notifications.view');
    Route::get('/notifications/{notification}', [NotificationController::class, 'redirectAndMarkAsRead'])->middleware('can:notifications.view');
    Route::get('/inbox', [InboxController::class, 'index'])->middleware('can:inbox.view');
    Route::put('/inbox/update-storage/{id}', [InboxController::class, 'updateBlockStorage'])->middleware('can:inbox.view');

    // ALMACENAMIENTO FISICO
    Route::prefix('sections')->group(function () {
        Route::get('/', [SectionController::class, 'index'])->middleware('can:sections.view');
        Route::post('/', [SectionController::class, 'store'])->middleware('can:sections.create');
        Route::get('/{section}', [SectionController::class, 'show'])->middleware('can:sections.view');
        Route::put('/{section}', [SectionController::class, 'update'])->middleware('can:sections.update');
        Route::delete('/{section}', [SectionController::class, 'destroy'])->middleware('can:sections.delete');

        Route::prefix('{section}/andamios')->group(function () {
            Route::get('/', [AndamioController::class, 'index'])->middleware('can:andamios.view');
            Route::post('/', [AndamioController::class, 'store'])->middleware('can:andamios.create');
            Route::put('/{andamio}', [AndamioController::class, 'update'])->middleware('can:andamios.update');
            Route::delete('/{andamio}', [AndamioController::class, 'destroy'])->middleware('can:andamios.delete');

            Route::prefix('{andamio}/boxes')->group(function () {
                Route::get('/', [BoxController::class, 'index'])->middleware('can:boxes.view');
                Route::post('/', [BoxController::class, 'store'])->middleware('can:boxes.create');
                Route::put('/{box}', [BoxController::class, 'update'])->middleware('can:boxes.update');
                Route::delete('/{box}', [BoxController::class, 'destroy'])->middleware('can:boxes.delete');

                Route::prefix('{box}/archivos')->group(function () {
                    Route::get('/', [ArchivoController::class, 'index'])->middleware('can:boxes.view');
                    Route::post('/{block}/move', [ArchivoController::class, 'moveToDefault'])->middleware('can:boxes.update');
                });
            });
        });
    });

    Route::delete('/admin/clear-all', [SystemController::class, 'clearAll'])
        ->middleware('can:clear-system')
        ->name('admin.clear_all');

    Route::get('/storage-link', function () {
        $exitCode = Artisan::call('storage:link');
        return response()->json(['ok' => $exitCode === 0, 'output' => Artisan::output()]);
    })->name('storage.link');
});
