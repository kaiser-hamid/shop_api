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

    public function ayncSelectSearch(Request $request)
    {
        $brands = $this->brandService->getBrandsForSelectSearch($request->input('name'));

        if($brands->isEmpty()){
            return $this->errorResponse(message: 'No brands found');
        }

        $brands = $brands->map(function($brand){
            return [
                'id' => $brand->id,
                'label' => $brand->name,
                'value' => $brand->id,
            ];
        });

        return $this->successResponse(message: 'Brands fetched successfully', data: $brands);
    }
}
