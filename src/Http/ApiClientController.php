<?php

namespace CompleteSolar\ApiClients\Http;

use App\Http\Controllers\Controller; # This is default laravel controller
use CompleteSolar\ApiClients\Models\ApiClient;
use Illuminate\Http\Request;

class ApiClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return array
     */
    public function index()
    {
        return ApiClient::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreApiClient  $request
     */
    public function store(StoreApiClient $request)
    {
        $apiClient = new ApiClient($request->validated());

        if(!$apiClient->save()) {
            return $apiClient;
        }

        return abort(400, 'Cannot save the API client.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Request  $request
     * @param  ApiClient  $apiClient
     */
    public function edit(ApiClient $apiClient)
    {
        return $apiClient;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  StoreApiClient  $request
     * @param  ApiClient  $apiClient
     */
    public function update(StoreApiClient $request, ApiClient $apiClient)
    {
        $validatedFields = $request->validated();

        if ($validatedFields['refresh_api_key']) {
            $apiClient->setApiKey();
        }
        unset($validatedFields['refresh_api_key']);

        $apiClient->fill($validatedFields);

        if(!$apiClient->update($validatedFields)) {
            return abort(400, 'Cannot save the API client.');
        }

        return $apiClient;
    }
}