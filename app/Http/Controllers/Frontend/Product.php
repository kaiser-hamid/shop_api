<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductOverviewResource;
use App\Services\ProductService;
use Illuminate\Http\Request;

class Product extends Controller
{
    /**
     * Product overview
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function productOverview(ProductService $productService, $slug)
    {
        $product = $productService->getProductBySlug($slug);

        if (!$product) {
            return $this->errorResponse(message: 'Product not found.');
        }
        
        return $this->successResponse(message: 'Product overview', data: ProductOverviewResource::make($product));
    }
}
