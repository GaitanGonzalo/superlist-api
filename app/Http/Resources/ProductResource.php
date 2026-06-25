<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'ean_code' => $this->ean_code,
            'brand_id' => $this->brand_id ? (int)$this->brand_id : null,
            'products_category_id' => $this->products_category_id ? (int)$this->products_category_id : null,
            'rubro_id' => $this->rubro_id,
            'caracteristics' => $this->caracteristics,
            'weight' => (int)$this->weight,
            'um' => $this->um,
            // Appends latest_price if it was dynamically loaded
            'latest_price' => $this->when(isset($this->latest_price), function () {
                return $this->latest_price !== null ? (float)$this->latest_price : null;
            }),
        ];
    }
}
