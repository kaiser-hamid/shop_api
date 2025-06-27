<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductOverviewResource;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Product overview
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function productOverview(ProductService $productService, string $slug)
    {
        $product = $productService->getProductBySlug($slug);

        if (!$product) {
            return $this->errorResponse(message: 'Product not found.');
        }
        
        return $this->successResponse(message: 'Product overview', data: ProductOverviewResource::make($product));
    }

    /**
     * Product additional info
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function productAdditionalInfo(ProductService $productService, int $id)
    {
        $product = $productService->getProductAdditionalInfo($id);
        
        if (!$product) {
            return $this->errorResponse(message: 'Product not found.');
        }

        $additionalInfo = [
            'description' => $product->description,
            'ingredient' => $product->ingredient?->description ?? null,
            'usage' => $product->usage?->description ?? null,
            'reviews' => null,
            'qna' => null,
        ];
        
        return $this->successResponse(message: 'Product additional info', data: $additionalInfo);
    }
}
