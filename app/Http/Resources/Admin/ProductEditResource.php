<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\HasFileInput;

class ProductEditResource extends JsonResource
{
    use HasFileInput;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'brand' => ['id' => $this->brand->id, 'label' => $this->brand->name, 'value' => $this->brand_id],
            'categories' => $this->categories->map(fn($category) => ['id' => $category->id, 'label' => $category->name, 'value' => $category->id]),
            'size' => $this->size,
            'galleries' => $this->fileInputs($this->productImages?->pluck('image_path')?->toArray()),
            'gallery_prev' => $this->productImages?->pluck('image_path')?->toArray(),
            'description' => $this->description,
            'ingredients' => $this->ingredient->description,
            'how_to_use' => $this->usage->description,
            'tags' => $this->tags?->map(fn($tag) => ['id' => $tag->id, 'label' => $tag->name, 'value' => $tag->id]),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => array_map(fn($keyword) => ['label' => $keyword, 'value' => $keyword], $this->meta_keywords ?? []),
            'featured_image' => $this->fileInput($this->featured_image),
            'status' => $this->status,
        ];
    }
}
