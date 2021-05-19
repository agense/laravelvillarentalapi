<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facility;

class FacilitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = storage_path() . "/app/data/facilities.json"; 
        $data = json_decode(file_get_contents($path), true);

        Facility::insert($data);
        
    }
    
}
