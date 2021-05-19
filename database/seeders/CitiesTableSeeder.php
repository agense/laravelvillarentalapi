<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Region;
use App\Models\City;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regions = Region::all('id','name')->pluck('id', 'name')->toArray();

        $path = storage_path() . "/app/data/locations.json"; 
        $data = json_decode(file_get_contents($path), true);

        //Format Data
        $cities = [];
        foreach($data as $item){
            foreach($item['cities'] as $city){
                $arr = [];
                $arr['name'] = $city['city'];
                $arr['slug'] = Str::slug($city['city'], '-');
                $arr['region_id'] = $regions[$item['region']];
                $cities[] = $arr;
            }
        }
        City::insert($cities);
    }
}
