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
            'id' => $this->product_id,
            'sku' => $this->sku,
            'name' => $this->product_name,
            'description' => $this->description,
            'price' => $this->price,
            'formatted_price' => $this->formatted_price,
            'stock_quantity' => $this->stock_quantity,
            'specifications' => $this->specifications,
            'status' => $this->status,
            'is_active' => $this->status === 'active',
            'is_in_stock' => $this->stock_quantity > 0 && $this->status === 'active',

            // Relationships
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->category_id,
                    'name' => $this->category->category_name,
                    'status' => $this->category->status,
                ];
            }),

            'brand' => $this->whenLoaded('brand', function () {
                return [
                    'id' => $this->brand->brand_id,
                    'name' => $this->brand->brand_name,
                    'status' => $this->brand->status,
                ];
            }),

            'primary_image' => $this->whenLoaded('primaryImage', function () {
                return [
                    'id' => $this->primaryImage->image_id,
                    'path' => $this->primaryImage->image_path,
                    'url' => asset('storage/' . $this->primaryImage->image_path),
                ];
            }),

            'images' => ProductImageResource::collection($this->whenLoaded('images')),

            'reviews' => $this->whenLoaded('reviews', function () {
                return [
                    'count' => $this->reviews->count(),
                    'average_rating' => $this->average_rating,
                    'rating_distribution' => $this->rating_distribution,
                ];
            }),

            // Computed attributes
            'average_rating' => $this->average_rating ?? 0,
            'total_reviews' => $this->total_reviews ?? 0,
            'views_count' => $this->views ?? 0,

            // Timestamps
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}