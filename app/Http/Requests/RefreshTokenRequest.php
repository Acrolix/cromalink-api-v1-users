<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RefreshTokenRequest extends FormRequest
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
            'refresh_token' => ['required', 'string'],
        ];
    }

    /**
     * Get the custom messages for the validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'refresh_token.required' => 'El token de refresco es requerido',
            'refresh_token.string' => 'El token de refresco debe ser una cadena',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message'   => 'Error de validación',
            'errors'      => $validator->errors()
        ], 400));
    }
}
