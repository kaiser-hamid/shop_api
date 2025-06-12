<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Requests\Admin\ProductRequest;
use App\Http\Resources\Admin\ProductResource;
use App\Http\Resources\Admin\ProductEditResource;
use App\Models\Product;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $products = $this->productService->getProductsPaginated();

        if ($products->isEmpty()) {
            return $this->errorResponse(message: 'No products found.');
        }
        
        $data = [
            'data' => ProductResource::collection($products->items()),
            'per_page' => $products->perPage(),
            'total' => $products->total()
        ];

        return $this->successResponse(message: 'Success! Products fetched.', data: $data);
    }

    
    public function store(ProductRequest $request)
    {
        $product = $this->productService->createProduct($request->validated());

        if(!$product){
            return $this->errorResponse(message: 'Failed! Product cannot be saved.');
        }

        return $this->successResponse(message: 'Success! Product created.');
    }

    public function edit(ProductService $productService, $product_id)
    {
        $product = $productService->getEditableProduct($product_id);

        if(!$product){
            return $this->errorResponse(message: 'Failed! Product not found.');
        }

        return $this->successResponse(message: 'Success! Product fetched.', data: ProductEditResource::make($product));
    }
    
    public function update(ProductRequest $request, Product $product)
    {
        $product = $this->productService->updateProduct($request->validated(), $product);

        if(!$product){
            return $this->errorResponse(message: 'Failed! Product cannot be updated.');
        }

        return $this->successResponse(message: 'Success! Product updated.');
    }



}
