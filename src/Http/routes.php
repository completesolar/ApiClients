<?php

use Illuminate\Support\Facades\Route;

Route::resource('api_clients', 'ApiClientController')
    ->only(['index', 'store', 'edit', 'update']);