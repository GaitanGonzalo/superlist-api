<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListItemRequest extends FormRequest
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
        $rules = [
            'uuid' => 'required|string',
            'product_id' => 'required|integer|exists:products,id',
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'is_purchased' => 'required|boolean',
        ];

        if ($this->isMethod('patch') || $this->isMethod('put')) {
            foreach ($rules as $key => $rule) {
                if (is_string($rule)) {
                    $rules[$key] = 'sometimes|' . $rule;
                }
            }
        }

        return $rules;
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'uuid.required' => 'El UUID del producto es requerido.',
            'product_id.required' => 'El ID del producto es requerido.',
            'product_id.exists' => 'El producto seleccionado no existe en el catálogo.',
            'name.required' => 'El nombre del producto es requerido.',
            'quantity.required' => 'La cantidad del producto es requerida.',
            'quantity.min' => 'La cantidad del producto debe ser al menos 1.',
            'price.required' => 'El precio del producto es requerido.',
            'price.min' => 'El precio del producto no puede ser negativo.',
            'is_purchased.required' => 'El estado de compra del producto es requerido.',
        ];
    }
}
