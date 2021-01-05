<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api/api-clients')->name('api.api_clients.')->group(function () {
    Route::get('', '\CompleteSolar\ApiClients\Http\ApiClientController@index')->name('index');
    Route::post('', '\CompleteSolar\ApiClients\Http\ApiClientController@store')->name('store');
    Route::get('', '\CompleteSolar\ApiClients\Http\ApiClientController@edit')
        ->name('edit');
    Route::put('', '\CompleteSolar\ApiClients\Http\ApiClientController@update')
        ->name('update');
    Route::put('scopes', '\CompleteSolar\ApiClients\Http\ApiClientController@updateScopes')
        ->name('update_scopes');
    Route::put(
        'scopes/{apiClientScope}/trigger',
        '\CompleteSolar\ApiClients\Http\ApiClientController@updateScopes'
    )
        ->name('trigger_scope');
});
