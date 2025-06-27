<?php

namespace App\Services;

use App\Models\City;

class CommonService
{
    public function cityAreaList()
    {
        $cities = City::select('id', 'name')->with(['areas' => fn ($query) => $query->active()])->active()->get();

        return $cities;
    }
}