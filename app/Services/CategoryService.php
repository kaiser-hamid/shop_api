<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Resources\Admin\CategoryResource;

class CategoryService
{
    /**
     * Get all categories with their relationships
     *
     * @return Collection
     */
    public function getAllCategories(): Collection
    {
        return Category::with(['parent', 'children'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get categories in hierarchical structure for admin dashboard
     *
     * @return Collection
     */
    public function getHierarchicalCategories(): Collection
    {
        return Category::with(['children.children'])
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get categories formatted for DataTable display
     *
     * @return array
     */
    public function getCategoriesForDataTable(): array
    {
        $categories = $this->getHierarchicalCategories();
        return CategoryResource::forDataTable($categories);
    }

    /**
     * Create a new category
     *
     * @param array $data
     * @return Category
     */
    public function createCategory(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Update a category
     *
     * @param int $id
     * @param array $data
     * @return Category|null
     */
    public function updateCategory(int $id, array $data): ?Category
    {
        $category = Category::find($id);
        
        if (!$category) {
            return null;
        }

        $category->update($data);
        return $category;
    }

    /**
     * Delete a category
     *
     * @param int $id
     * @return bool
     */
    public function deleteCategory(int $id): bool
    {
        $category = Category::find($id);
        
        if (!$category) {
            return false;
        }

        // Check if category has children
        if ($category->children()->count() > 0) {
            return false;
        }

        return $category->delete();
    }

    /**
     * Get a single category
     *
     * @param int $id
     * @return Category|null
     */
    public function getCategory(int $id): ?Category
    {
        return Category::with(['parent', 'children'])->find($id);
    }

    public function getCategoriesForSelectSearch($query)
    {
        return Category::active()->where('name', 'like', "%$query%")->get();
    }

} 