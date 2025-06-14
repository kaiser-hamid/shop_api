<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Requests\Admin\ProductRequest;
use App\Http\Resources\Admin\ProductResource;
use App\Http\Resources\Admin\ProductEditResource;
use App\Models\Product;
use App\Http\Requests\Admin\ProductVariantRequest;
use App\Http\Requests\Admin\ProductVariantPatchRequest;
use App\Models\ProductVariant;

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

    /* Products */
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

    /* Product variants */
    public function getProductVariants($product_id)
    {
        $productVariants = $this->productService->getProductVariants($product_id);

        $product = [
            'id' => $productVariants->id,
            'name' => $productVariants->name,
            'slug' => $productVariants->slug,
        ];
        $variants = $productVariants->variants;
        $data = [
            'product' => $product,
            'variants' => $variants
        ];
        return $this->successResponse(message: 'Success! Product variants fetched.', data: $data);
    }

    public function storeProductVariant(ProductVariantRequest $request, Product $product)
    {
        $productVariant = $this->productService->createProductVariant($request->validated(), $product);

        return $this->successResponse(message: 'Success! Product variant created.', data: $productVariant);
    }

    public function updateProductVariant(ProductVariantPatchRequest $request, ProductVariant $productVariant)
    {
        $productVariant = $this->productService->updateProductVariant($request->validated(), $productVariant);

        return $this->successResponse(message: 'Updated successfully.', data: $productVariant);
    }

    public function deleteProductVariant(ProductVariant $productVariant)
    {
        $productVariant->delete();
        return $this->successResponse(message: 'Deleted successfully.');
    }


}
