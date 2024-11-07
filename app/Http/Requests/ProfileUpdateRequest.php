<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->user()->id == $this->route('id')) 
            return false;

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
            'first_name' => 'required|string|max:255',
            'last_name' => 'string|max:255',
            'biography' => 'string|max:255',
            'avatar' => 'file|required|mimes:jpeg,png,jpg,gif|max:5120', 
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
            'avatar.max' => 'El avatar debe ser menor a 5120kb!',
            'avatar.mimes' => 'Solo se aceptan estos tipos de archivo: jpeg, png, jpg, gif!',
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
