<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/home.php';
require __DIR__.'/users.php';
require __DIR__.'/documents.php';
require __DIR__.'/blocks.php';
require __DIR__.'/roles.php';
require __DIR__.'/storage.php';
require __DIR__.'/inbox.php';
require __DIR__.'/document_types.php';
require __DIR__.'/documentary_series.php';
require __DIR__.'/areas.php';
require __DIR__.'/activities.php';
require __DIR__.'/configuration.php';

Route::get('/up', function () {
    return response()->noContent();
});
