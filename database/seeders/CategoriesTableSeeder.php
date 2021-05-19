<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoriesTableSeeder extends Seeder
{
   /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $categories = ['Luxury','Sea Front', 'City', 'Country'];
        $data = [];

        foreach($categories as $category){
            $arr = [];
            $arr["name"] = $category;
            $arr["slug"] = Str::slug($category, '-');
            $data[] = $arr;
        }

        Category::insert($data);
    }
}
