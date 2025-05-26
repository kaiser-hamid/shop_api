<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BrandService;
use App\Http\Resources\Admin\BrandResource;
class BrandController extends Controller
{
    protected $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    public function index()
    {
        $brands = $this->brandService->getAllBrands();
       
        $brands = BrandResource::collection($brands);

        return $this->successResponse(message: 'Brands fetched successfully', data: $brands);
    }
}
