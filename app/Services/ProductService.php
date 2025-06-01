<?php

namespace App\Services;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\ProductImage;


class ProductService
{
    public function getAllProducts()
    {
        return Product::all();
    }

    /* Admin */
    public function createProduct(array $data)
    {
        DB::beginTransaction();
        try {
            $product = new Product();
            $product->name = $data['name'];
            $product->brand_id = $data['brand_id'];
            $product->featured_image = request()->file('featured_image');
            $product->description = $data['description'];
            $product->meta_title = $data['meta_title'] ?? null;
            $product->meta_description = $data['meta_description'] ?? null;
            $product->meta_keywords = $data['meta_keywords'] ?? null;
            $product->status = $data['status'];
            $product->save();

            $product->categories()->attach($data['categories']);

            $galleries = [];
            foreach (request()->file('galleries') as $gallery) {
                $galleries[] = new ProductImage([
                    'image_path' => $gallery,
                ]);
            }
            $product->productImages()->saveMany($galleries);

            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    
}