<?php

namespace App\Services;

use App\Models\Brand;

class BrandService
{
    public function getAllBrands()
    {
        return Brand::select('id', 'name', 'is_featured', 'is_top', 'status')->orderBy('name')->get();
    }
}
