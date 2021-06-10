<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Account;
use Illuminate\Support\Facades\Hash;

class AccountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       //Create an account of each type
       $accountTypes = Account::getAccountTypes();
       $i = 1;
       foreach($accountTypes as $type){
           $account =  new Account();
           $account->fill([
            'number' => '12345789',
            'company_name' => "Test Company $i",
            'company_registration_number' => rand(1000000, 9990000), 
            'company_owner_name' => "John Doe",
            'company_email' => "test$i@test.com",
            'company_phone' => rand(1000000, 9990000),
            'company_address'=> "Test Str 101",
            'company_city' => "Any City",
            'company_country' => 'Any Country',
           ]);
           $account->setType($type);
           $account->save();
           
   
           $user = new User();
           $user->name = "John Doe";
           $user->email = "test$i@test.com";
           $user->password = Hash::make('password');
           $user->type = 2;
           $user->account()->associate($account);
           $user->save();
           $i++;
       }
    }   
}
