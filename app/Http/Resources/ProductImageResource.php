<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->image_id,
            'product_id' => $this->product_id,
            'image_path' => $this->image_path,
            'url' => asset('storage/' . $this->image_path),
            'is_primary' => $this->is_primary,
            'sort_order' => $this->sort_order,
            'uploader' => $this->whenLoaded('uploader', function () {
                return [
                    'id' => $this->uploader->id,
                    'name' => $this->uploader->name,
                ];
            }),
            'created_at' => $this->created_at,
        ];
    }
}