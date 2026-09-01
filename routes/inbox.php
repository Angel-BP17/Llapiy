<?php

use App\Http\Controllers\Inbox\InboxController;
use App\Http\Controllers\Inbox\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/bandeja', [InboxController::class, 'index'])->name('inbox.index')->middleware('can:inbox.view');
    Route::put('/inbox/update-storage/{id}', [InboxController::class, 'updateStorage'])->name('inbox.updateStorage');
    Route::delete('/inbox/delete-file/{id}', [InboxController::class, 'deleteFile'])->name('inbox.deleteFile');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}', [NotificationController::class, 'read'])->name('notifications.read');
});
