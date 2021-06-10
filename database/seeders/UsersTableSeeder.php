<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       //Create a System Admin Type of User
       User::createSystemAdminUser([
           'name' => 'Admin User',
           'email' => 'admin@test.com',
           'password' => 'password'
       ]);
    }
}
