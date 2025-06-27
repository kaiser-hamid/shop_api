<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductOverviewResource extends JsonResource
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
            'slug' => $this->slug,
            'size' => $this->size,
            'sku' => $this->topVariant?->sku ?? null,
            'old_price' => $this->getOldPrice(),
            'sale_price' => $this->getSalePrice(),
            'discount' => $this->topVariant?->discount_percentage ? round($this->topVariant?->discount_percentage, 2) : null,
            'price_saved' => $this->getPriceSaved(),
            'stock_quantity' => $this->topVariant?->stock_quantity ?? 0,
            'low_stock_status' => $this->lowStockStatus(),
            'reviews_count' => rand(10, 100),
            'rating' => round(rand(10, 50) / 10, 1),
            'brief_description' => $this->brief_description,
            'brand' => $this->brand ? ['slug' => $this->brand->slug, 'name' => $this->brand->name] : null,
            'categories' => $this->getCategories(),
            'tags' => $this->tags?->pluck('name') ?? [],
            'is_available' => $this->topVariant?->stock_quantity > 0 ? true : false,
            'product_images' => $this->getProductImages(),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
        ];
    }

    private function getOldPrice()
    {
        $price = $this->topVariant?->price ?? null;

        if (!$price) {
            return null;
        }

        $discountPercentage = $this->topVariant?->discount_percentage ?? null;   

        if (!$discountPercentage) {
            return null;
        }

        return number_format(ceil($price), 2);
    }

    private function getSalePrice()
    {
        $price = $this->topVariant?->price ?? null;

        if (!$price) {
            return 0;
        }

        $discountPercentage = $this->topVariant?->discount_percentage ?? null;   

        if (!$discountPercentage) {
            return number_format(ceil($price), 2);
        }

        return number_format(ceil($price - ($price * $discountPercentage / 100)), 2);
    }

    private function getPriceSaved()
    {
        $oldPrice = $this->getOldPrice();

        if (!$oldPrice) {
            return null;
        }

        $salePrice = $this->getSalePrice();

        return number_format(ceil($oldPrice - $salePrice), 2);
    }

    private function lowStockStatus()
    {
        $stockQuantity = $this->topVariant?->stock_quantity ?? 0;

        if(!$stockQuantity) {
            return null;
        }

        $lowStockThreshold = $this->topVariant?->low_stock_threshold ?? 0;
        
        if ($stockQuantity > $lowStockThreshold) {
            return null;
        }

        $item = $stockQuantity > 1 ? 'items' : 'item';

        return "Only {$stockQuantity} {$item} left in stock";
    }

    private function getProductImages()
    {
        $images = $this->productImages?->pluck('image_path')?->map(fn($image) => asset('storage/' . $image))?->toArray() ?? [];

        $featuredImage = $this->featured_image ? asset('storage/' . $this->featured_image) : null;

        if (!$featuredImage) {
            return $images;
        }

        return [$featuredImage, ...$images];
    }

    private function getCategories()
    {
        return $this->categories?->map(fn($item) => ["slug" => $item->slug, "name" => $item->name]) ?? [];
    }

}
