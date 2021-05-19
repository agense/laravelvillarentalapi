<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use Illuminate\Support\Str;

class RegionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = storage_path() . "/app/data/locations.json"; 
        $data = json_decode(file_get_contents($path), true);
        
        $data = collect($data)->map(function ($item, $index){
            $arr = [];
            $arr['name'] = $item['region'];
            $arr['slug'] = Str::slug($item['region'], '-');
            return $arr;
        })->toArray();

        Region::insert($data);
    }
}
