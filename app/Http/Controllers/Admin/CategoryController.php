<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Admin\CategoryResource;


class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Get all categories formatted for DataTable
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getHierarchicalCategories();

        $categories = CategoryResource::forDataTable($categories);

        return $this->successResponse('Categories fetched successfully', $categories);
    }

    public function ayncSelectSearch(Request $request) : JsonResponse
    {
        $categories = $this->categoryService->getCategoriesForSelectSearch($request->input('name'));

        if($categories->isEmpty()){
            return $this->errorResponse('No categories found');
        }

        $categories = $categories->map(function($category){
            return [
                'id' => $category->id,
                'label' => $category->name,
                'value' => $category->id,
            ];
        });

        return $this->successResponse('Categories fetched successfully', $categories);
    }

} 