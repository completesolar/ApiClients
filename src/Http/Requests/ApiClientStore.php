<?php

namespace CompleteSolar\ApiClients\Http\Requests;

use Illuminate\Validation\Rule; # laravel built-in validation rule
use Illuminate\Foundation\Http\FormRequest; # laravel built-in form request

class ApiClientStore extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'unique:api_clients', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'webhook_url' => ['required', 'string'],
            'scopes' => ['nullable', 'array', 'min:1'],
            'scopes.*' => [
                Rule::requiredIf(is_array($this->input('scopes'))),
                'string',
                'exists:api_client_scopes,name'
            ]
        ];
    }
}
