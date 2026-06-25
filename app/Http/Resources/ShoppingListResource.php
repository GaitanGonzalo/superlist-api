<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShoppingListResource extends JsonResource
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
            'user_id' => (int)$this->user_id,
            'store_id' => $this->store_id,
            'store_name' => $this->store_name,
            'store_address' => $this->store_address,
            'is_finished' => (int)$this->is_finished,
            'finished_at' => $this->finished_at,
            'total_spent' => (float)$this->total_spent,
            'actual_total' => (float)$this->actual_total,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'items' => ShoppingListItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
