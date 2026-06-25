<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShoppingListItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'shopping_list_id' => (int)$this->shopping_list_id,
            'product_id' => (int)$this->product_id,
            'name' => $this->name,
            'quantity' => (int)$this->quantity,
            'price' => (float)$this->price,
            'is_purchased' => (int)$this->is_purchased,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
