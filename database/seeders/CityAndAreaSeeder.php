<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Models\City;
use App\Models\Area;

class CityAndAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = json_decode(file_get_contents(database_path('seeders/source/cityAndArea.json')), true);

        City::truncate();
        Area::truncate();

        foreach ($cities as $city => $areas) {
            $city = City::create(['name' => $city]);
            
            $areaData = [];
            foreach ($areas as $area) {
                $areaData[] = new Area(['name' => $area]);
            }
            
            $city->areas()->saveMany($areaData);
        }
    }
}
