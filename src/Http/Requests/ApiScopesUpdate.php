<?php

namespace CompleteSolar\ApiClients\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiScopesUpdate extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'scopes' => ['required', 'array', 'min:1'],
            'scopes.*' => ['string', 'exists:api_client_scopes,name'],
            'sync' => ['nullable', 'boolean'],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'sync' => !! $this->input('sync'),
        ]);
    }
}
