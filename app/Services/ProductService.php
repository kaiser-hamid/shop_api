<?php

namespace App\Services;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\ProductImage;
use App\Models\ProductIngredient;
use App\Models\ProductUsage;
use App\Models\Tag;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Log;

class ProductService
{

    /*==================================================== FRONTEND FUNCTIONS ====================================================*/
    
    /**
     * Get product by slug
     * @param string $slug
     * @return Product
     */
    public function getProductBySlug($slug)
    {
        return Product::select(['id', 'name', 'slug', 'size', 'featured_image', 'brief_description', 'meta_title', 'meta_description', 'meta_keywords', 'brand_id'])
        ->with(['topVariant:id,product_id,sku,price,discount_percentage,stock_quantity,low_stock_threshold', 'brand:id,name,slug', 'categories:name,slug', 'tags:id,name', 'productImages:id,product_id,image_path'])
        ->active()
        ->where('slug', $slug)
        ->first();
    }


    /**
     * Get product additional info
     * @param int $id
     * @return Product
     */
    public function getProductAdditionalInfo(int $id)
    {
        return Product::select(['id', 'description'])
        ->with(['ingredient:id,product_id,description', 'usage:id,product_id,description'])
        ->active()
        ->where('id', $id)
        ->first();
    }

    /*================================================= FRONTEND FUNCTIONS END ================================================*/


    /*==================================================== ADMIN FUNCTIONS ====================================================*/

    /**
     * Get products paginated
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getProductsPaginated()
    {
        $queryParams = request()->query('search');
        return Product::with(['brand:id,name', 'categories:name'])->where(function($query) use ($queryParams) {
            if($queryParams) {
                $query->where('name', 'like', '%' . $queryParams . '%');
            }
        })->orWhereHas('brand', function($query) use ($queryParams) {
            if($queryParams) {
                $query->where('name', 'like', '%' . $queryParams . '%');
            }
        })->orWhereHas('categories', function($query) use ($queryParams) {
            if($queryParams) {
                $query->where('name', 'like', '%' . $queryParams . '%');
            }
        })->paginate(20);
    }


    /**
     * Create product
     * @param array $data
     * @return Product
     */
    public function createProduct(array $data)
    {
        DB::beginTransaction();
        try {
            $product = new Product();
            $product->name = $data['name'];
            $product->size = $data['size'] ?? null;
            $product->brand_id = $data['brand_id'];
            $product->featured_image = request()->file('featured_image');
            $product->description = $data['description'];
            /* Meta data */
            $product->meta_title = $data['meta_title'] ?? null;
            $product->meta_description = $data['meta_description'] ?? null;
            $product->meta_keywords = $data['meta_keywords'] ?? null;
            /* End meta data */
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
            
            if(request()->has('ingredients')){
                $ingredients = new ProductIngredient(['description' => $data['ingredients']]);
                $product->ingredient()->save($ingredients);
            }

            if(request()->has('how_to_use')){
                $usage = new ProductUsage([
                    'description' => $data['how_to_use'],
                ]);
                $product->usage()->save($usage);
            }

            if(request()->has('tags')){
                $tagIds = [];
                foreach($data['tags'] as $tagName) {
                    $tag = Tag::firstOrCreate(['name' => $tagName]);
                    $tagIds[] = $tag->id;
                }
                $product->tags()->attach($tagIds);
            }

            $product->load('categories', 'brand');
            $product->searchable();

            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get editable product
     * @param int $product_id
     * @return Product
     */
    public function getEditableProduct($product_id)
    {
        return Product::with(['brand', 'categories', 'tags', 'ingredient', 'usage', 'productImages'])->find($product_id);
    }

    /**
     * Update product
     * @param array $data
     * @param Product $product
     * @return Product
     */
    public function updateProduct(array $data, Product $product)
    {
        DB::beginTransaction();
        try {
            $product->name = $data['name'];
            $product->size = $data['size'] ?? null;
            $product->brand_id = $data['brand_id'];
            if (request()->hasFile('featured_image')) {
                $product->featured_image = request()->file('featured_image');
            }
            $product->description = $data['description'];
            /* Meta data */
            $product->meta_title = $data['meta_title'] ?? null;
            $product->meta_description = $data['meta_description'] ?? null;
            $product->meta_keywords = $data['meta_keywords'] ?? null;
            /* End meta data */
            $product->status = $data['status'];
            $product->save();

            // Sync categories to replace existing ones
            $product->categories()->sync($data['categories']);

            /* Delete old gallery images */
            $imagesToDelete = [];
            if(request()->has('galleries_exists')){
                 $imagesToDelete = ProductImage::where('product_id', $product->id)
                    ->whereNotIn('image_path', $data['galleries_exists'])
                    ->get();                
            } else {
                $imagesToDelete = ProductImage::where('product_id', $product->id)->get();
            }
            foreach($imagesToDelete as $image) {
                $image->delete();
            }

            // Handle new gallery images if provided
            if (request()->has('galleries') && request()->hasFile('galleries.*')) {
                $galleries = [];
                foreach (request()->file('galleries') as $gallery) {
                    $galleries[] = new ProductImage([
                        'image_path' => $gallery,
                    ]);
                }
                $product->productImages()->saveMany($galleries);
            }
            
            // Update or create ingredients
            if(request()->has('ingredients')){
                if ($product->ingredient) {
                    $product->ingredient->update(['description' => $data['ingredients']]);
                } else {
                    $ingredients = new ProductIngredient(['description' => $data['ingredients']]);
                    $product->ingredient()->save($ingredients);
                }
            }

            // Update or create usage instructions
            if(request()->has('how_to_use')){
                if ($product->usage) {
                    $product->usage->update(['description' => $data['how_to_use']]);
                } else {
                    $usage = new ProductUsage([
                        'description' => $data['how_to_use'],
                    ]);
                    $product->usage()->save($usage);
                }
            }

            // Sync tags to replace existing ones
            if(request()->has('tags')){
                $tagIds = [];
                foreach($data['tags'] as $tagName) {
                    $tag = Tag::firstOrCreate(['name' => $tagName]);
                    $tagIds[] = $tag->id;
                }
                $product->tags()->sync($tagIds);
            }

            $product->load('categories', 'brand');
            $product->searchable();

            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getProductVariants($product_id)
    {
        return Product::with('variants:id,product_id,sku,price,discount_percentage,stock_quantity,status')->select(['id', 'name', 'slug'])->findOrFail($product_id);
    }

    public function createProductVariant(array $data, Product $product)
    {
        $productVariant = new ProductVariant($data);

        $isSaved = $product->variants()->save($productVariant);
        
        if(!$isSaved) {
            throw new \Exception('Failed to create product variant');
        }

        $productVariant = $productVariant->fresh();

        $product->searchable();
        
        return [
            'id' => $productVariant->id,
            'product_id' => $productVariant->product_id,
            'sku' => $productVariant->sku,
            'price' => $productVariant->price,
            'discount_percentage' => $productVariant->discount_percentage,
            'stock_quantity' => $productVariant->stock_quantity,
            'status' => $productVariant->status,
        ];
    }

    public function updateProductVariant(array $data, ProductVariant $productVariant)
    {
        $isUpdated = $productVariant->update($data);
        
        if (!$isUpdated) {
            throw new \Exception('Failed to update product variant');
        }

        $productVariant->product->searchable();
        
        return $productVariant;
    }
    
    
}