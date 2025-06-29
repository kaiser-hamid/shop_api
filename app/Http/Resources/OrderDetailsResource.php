<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'image' => $this->product->featured_image ? asset('storage/' . $this->product->featured_image) : null,
            'name' => $this->product->name,
            'slug' => $this->product->slug,
            'quantity' => $this->quantity,
            'unit_price' => $this->price,
            'total_price' => $this->total_price,
        ];
    }
}
