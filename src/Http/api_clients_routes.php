<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api')->name('api.')->group(function () {
    Route::resource('clients', '\CompleteSolar\ApiClients\Http\ApiClientController')
        ->only(['index', 'store', 'edit', 'update']);
});
