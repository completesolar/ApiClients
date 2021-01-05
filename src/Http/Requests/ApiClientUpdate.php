<?php

namespace CompleteSolar\ApiClients\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use CompleteSolar\ApiClients\Models\ApiClient;

class ApiClientUpdate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [
                'nullable',
                Rule::unique('api_clients')->ignore($this->header(ApiClient::getHeaderKey()), 'api_key'),
                'max:255'
            ],
            'is_active' => ['nullable', 'boolean'],
            'webhook_url' => ['nullable', 'string'],
            'refresh_api_key' => ['nullable', 'boolean'],
        ];
    }
}
