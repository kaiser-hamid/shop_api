<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Requests\Admin\ProductRequest;
class ProductController extends Controller
{
    public function __construct(private ProductService $productService)
    {
        $this->productService = $productService;
    }

    
    public function store(ProductRequest $request)
    {
        $product = $this->productService->createProduct($request->validated());

        if(!$product){
            return $this->errorResponse(message: 'Failed! Product cannot be saved.');
        }

        return $this->successResponse(message: 'Success! Product created.');
    }



}
