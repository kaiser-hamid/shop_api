<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class CategoryResource extends JsonResource
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
            'description' => $this->description,
            'is_active' => $this->is_active,
            'parent_id' => $this->parent_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'parent' => $this->whenLoaded('parent'),
            'children' => $this->whenLoaded('children'),
        ];
    }

    /**
     * Format categories for DataTable display
     *
     * @param Collection $categories
     * @return array
     */
    public static function forDataTable(Collection $categories): array
    {
        $formattedCategories = [];

        foreach ($categories as $mainCategory) {
            // Add main category
            $formattedCategories[] = [
                'id' => $mainCategory->id,
                'main_category' => $mainCategory->name,
                'sub_category' => '-',
                'sub_sub_category' => '-',
                'is_active' => $mainCategory->is_active,
            ];

            // Add subcategories
            foreach ($mainCategory->children as $subCategory) {
                $formattedCategories[] = [
                    'id' => $subCategory->id,
                    'main_category' => $mainCategory->name,
                    'sub_category' => $subCategory->name,
                    'sub_sub_category' => '-',
                    'is_active' => $subCategory->is_active,
                ];

                // Add sub-subcategories
                foreach ($subCategory->children as $subSubCategory) {
                    $formattedCategories[] = [
                        'id' => $subSubCategory->id,
                        'main_category' => $mainCategory->name,
                        'sub_category' => $subCategory->name,
                        'sub_sub_category' => $subSubCategory->name,
                        'is_active' => $subSubCategory->is_active,
                    ];
                }
            }
        }

        return $formattedCategories;
    }
}
