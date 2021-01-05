<?php

namespace CompleteSolar\ApiClients\Http;

use App\Http\Controllers\Controller; # This is default laravel controller
use CompleteSolar\ApiClients\Models\ApiClient;
use CompleteSolar\ApiClients\Models\ApiClientScope;
use CompleteSolar\ApiClients\Http\Requests\ApiClientStore;
use CompleteSolar\ApiClients\Http\Requests\ApiClientUpdate;
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

    public function store(ApiClientStore $request)
    {
        $apiClient = new ApiClient($request->validated());

        if(!$apiClient->save()) {
            return abort(400, 'Cannot save the API client.');
        }

        $apiClient->scopes()->attach(
            ApiClientScope::findByNames($request->input('scopes', []))->pluck('id')->toArray()
        );

        return $apiClient;
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

    public function update(ApiClientUpdate $request)
    {
        $apiClient = $this->findApiClientByApiKey($request);

        if ($request->input('refresh_api_key')) {
            $apiClient->setApiKey();
        }

        if(!$apiClient->update($request->validated())) {
            return abort(400, 'Cannot save the API client.');
        }

        return $apiClient;
    }

    public function updateScopes(Request $request)
    {
        $apiClient = $this->findApiClientByApiKey($request);

        $apiClient->scopes()->sync(
            ApiClientScope::findByNames($request->input('scopes'))->pluck('id')->toArray(),
            $request->input('sync')
        );

        return $apiClient;
    }

    public function triggerScope(Request $request, ApiClientScope $apiClientScope)
    {
        $apiClient = $this->findApiClientByApiKey($request);

        if ($apiClient->scopes()->name($apiClientScope->name)->exists()) {
            $apiClient->scopes()->detach($apiClientScope);
        } else {
            $apiClient->scopes()->attach($apiClientScope);
        }

        return $apiClient;
    }

    protected function findApiClientByApiKey(Request $request): ApiClient
    {
        return ApiClient::findByApiKey($request->header(ApiCLient::getHeaderKey()));
    }
}
