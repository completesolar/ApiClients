<?php

namespace CompleteSolar\ApiClients\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiScopesUpdate extends FormRequest
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
            'scopes' => ['required', 'array', 'min:1'],
            'scopes.*' => ['string', 'exists:api_client_scopes,name'],
            'sync' => ['nullable', 'boolean'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'sync' => !! $this->input('sync'),
        ]);
    }
}
