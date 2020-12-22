<?php

use Illuminate\Support\Facades\Route;

Route::resource('api_clients', '\CompleteSolar\ApiClients\Http\ApiClientController')
    ->only(['index', 'store', 'edit', 'update']);