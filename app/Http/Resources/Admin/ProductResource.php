<?php

namespace App\Http\Resources\Admin;

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
            'slug' => $this->slug,
            'featured_image' => $this->getFeaturedImageThumbnail($this->featured_image),
            'brand' => $this->brand->name,
            'categories' => $this->categories->pluck('name'),
            'status' => $this->status,
        ];
    }

    private function getFeaturedImageThumbnail($featuredImage)
    {
        if(!$featuredImage) return null;

        $path = pathinfo($featuredImage, PATHINFO_DIRNAME);
        $filename = pathinfo($featuredImage, PATHINFO_BASENAME);
        $thumbnail = 'storage/' . $path . '/thumb/' . $filename;

        if (!file_exists(public_path($thumbnail))) {
            return null;
        }

        return asset($thumbnail);
    }
}
