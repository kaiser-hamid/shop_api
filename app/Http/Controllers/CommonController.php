<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CommonService;
use App\Http\Resources\CityAreaResource;

class CommonController extends Controller
{
    protected $commonService;

    public function __construct(CommonService $commonService)
    {
        $this->commonService = $commonService;
    }

    public function cityAreaList(Request $request)
    {
        $cities = $this->commonService->cityAreaList();

        if(!$cities) {
            return $this->errorResponse(message: 'No data found');
        }

        $cityList = $cities->pluck('name');
        
        $data = [
            'cities' => $cityList,
            'areas' => []//CityAreaResource::collection($cities)
        ];

        foreach($cities as $city) {
            $data['areas'][$city->name] = $city->areas->pluck('name');
        }

        return $this->successResponse(message: 'City list fetched successfully', data: $data);
    }
}
