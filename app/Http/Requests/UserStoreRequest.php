<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;

class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'last_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                ValidationRule::unique('users', 'email')->where(function ($query) {
                    $query->where('deleted', 0);
                }),
            ],
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'location_id' => 'required|exists:locations,id',
            'address' => 'nullable|string|max:255',
            'address_number' => 'nullable|string|max:255',
            'cel_number' => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'last_name.required' => 'El Apellido es requerido',
            'name.required' => 'El Nombre es requerido',
            'email.required' => 'El Email es requerido',
            'email.email' => 'El Email debe ser un correo válido',
            'country_id.required' => 'El País es requerido',
            'state_id.required' => 'La Provincia es requerida',
            'location_id.required' => 'La Localidad es requerida',
            'password.required' => 'La contraseña es requerida',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
        ];
    }
}
