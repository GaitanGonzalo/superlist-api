<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListRequest extends FormRequest
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
            'uuid' => 'nullable|string',
            'store_id' => 'nullable|string',
            'store_name' => 'nullable|string|max:255',
            'store_address' => 'nullable|string|max:255',
            'is_finished' => 'nullable|boolean',
            'finished_at' => 'nullable|date_format:Y-m-d H:i:s',
            'total_spent' => 'nullable|numeric',
            'actual_total' => 'nullable|numeric',
            'product_list' => 'nullable|array',
            'product_list.*.uuid' => 'required|string',
            'product_list.*.product_id' => 'required|integer|exists:products,id',
            'product_list.*.name' => 'required|string|max:255',
            'product_list.*.quantity' => 'required|integer|min:1',
            'product_list.*.price' => 'required|numeric|min:0',
            'product_list.*.is_purchased' => 'required|boolean',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'total_spent.required' => 'El total gastado es requerido.',
            'total_spent.numeric' => 'El total gastado debe ser un número.',
            'actual_total.required' => 'El total actual es requerido.',
            'actual_total.numeric' => 'El total actual debe ser un número.',
            'product_list.array' => 'La lista de productos debe ser un arreglo.',
            'product_list.*.uuid.required' => 'El UUID de cada producto en la lista es requerido.',
            'product_list.*.product_id.required' => 'El ID del producto es requerido.',
            'product_list.*.product_id.exists' => 'El producto seleccionado no existe en el catálogo.',
            'product_list.*.name.required' => 'El nombre del producto es requerido.',
            'product_list.*.quantity.required' => 'La cantidad del producto es requerida.',
            'product_list.*.quantity.min' => 'La cantidad del producto debe ser al menos 1.',
            'product_list.*.price.required' => 'El precio del producto es requerido.',
            'product_list.*.price.min' => 'El precio del producto no puede ser negativo.',
            'product_list.*.is_purchased.required' => 'El estado de compra del producto es requerido.',
        ];
    }
}
