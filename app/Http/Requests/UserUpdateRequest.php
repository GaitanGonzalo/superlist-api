<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;

class UserUpdateRequest extends FormRequest
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
            'last_name' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                ValidationRule::unique('users', 'email')->ignore($this->user()->id)->where(function ($query) {
                    $query->where('deleted', 0);
                }),
            ],
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'location_id' => 'nullable|exists:locations,id',
            'address' => 'nullable|string|max:255',
            'address_number' => 'nullable|string|max:255',
            'cel_number' => 'nullable|string|max:255',
        ];
    }
}
