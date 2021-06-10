<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       $this->call(UsersTableSeeder::class);
       $this->call(AccountsTableSeeder::class);
       $this->call(CategoriesTableSeeder::class);
       $this->call(FacilitiesTableSeeder::class);
       $this->call(RegionsTableSeeder::class);
       $this->call(CitiesTableSeeder::class);
       $this->call(VillasTableSeeder::class);
    }
}
