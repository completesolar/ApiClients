<?php

namespace CompleteSolar\ApiClients\Http;

use Illuminate\Foundation\Http\FormRequest; # laravel built-in form request

class StoreApiClient extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $apiClient = (int) $this->route('api_client');

        return [
            'name' => 'required|unique:api_clients,name,' . $apiClient . '|max:255',
            'is_active' => 'required|boolean',
            'webhook_url' => 'nullable',
            'refresh_api_key' => 'nullable|boolean',
        ];
    }
}
