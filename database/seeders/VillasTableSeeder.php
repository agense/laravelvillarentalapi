<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\User;
use App\Models\Villa;
use App\Models\Account;
use App\Models\Category;
use App\Models\Facility;
use App\Models\VillaImage;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class VillasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       //Get a supplier account type, if it does not exist, create one
        $account = Account::where('account_type', Account::getTypeValue('SUPPLIER'))->first();
        if(!$account){
           $account =  new Account();
           $account->fill([
            'number' => '12345789',
            'company_name' => "Test Company",
            'company_registration_number' => rand(1000000, 9990000), 
            'company_owner_name' => "John Doe",
            'company_email' => "test@test.com",
            'company_phone' => rand(1000000, 9990000),
            'company_address'=> "Test Str 101",
            'company_city' => "Any City",
            'company_country' => 'Any Country',
           ]);
           $account->setType('SUPPLIER');
           $account->save();
           
           $user = new User();
           $user->name = "John Doe";
           $user->email = "test@test.com";
           $user->password = Hash::make('password');
           $user->setType('CLIENT');
           $user->account()->associate($account);
           $user->save();
        }

        $count = 3;
        $image_path = config('filesystems.images.upload_location').'villas/';

        for($i =1; $i <= $count; $i++){
            $data = [
                'name' => 'Villa '.$i,
                'slug' => Str::slug('Villa '.$i, '-'),
                'area' => rand('45','150'),
                'capacity' => rand('2','10'),
                'bedrooms' => rand('1','4'),
                'bathrooms' => rand('1','4'),
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam aliquet enim ac iaculis tristique. Lorem ipsum dolor sit amet, consectetur adipiscing elit. In ante neque, posuere eget lectus eget, sodales tincidunt urna. Nulla placerat cursus tortor et tincidunt. Mauris convallis interdum urna. Nullam pellentesque sapien mi, at imperdiet lectus laoreet lobortis. Pellentesque in libero eget massa maximus vehicula. Pellentesque pretium nec diam sed aliquet. Pellentesque eu orci nec mi tempor posuere. Proin sollicitudin a orci at tincidunt. Quisque ligula nibh, convallis vitae rutrum eu, viverra quis mauris. Mauris nulla augue, molestie sed semper eget, tempus ac justo. Nam ut ullamcorper elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam aliquet enim ac iaculis tristique. Lorem ipsum dolor sit amet, consectetur adipiscing elit. In ante neque, posuere eget lectus eget, sodales tincidunt urna. Nulla placerat cursus tortor et tincidunt. ',
                'city_id' => City::inRandomOrder()->first()->id,
                'address' => 'Test str '. rand('1','100'),
            ];
            
            $villa = new Villa();
            $villa->fill($data);
            $villa->account()->associate($account);
            $villa->save();

             //attach falilities
             $facilities = Facility::inRandomOrder()->limit(10)->get('id')->pluck('id')->toArray();
             $villa->facilities()->attach($facilities);
             
             //attach categories
             $categories = Category::inRandomOrder()->limit(2)->get('id')->pluck('id')->toArray();
             $villa->categories()->attach($categories);
             
             //attach images
             $images = [];
             $nr = 1;
             while(Storage::exists($image_path.$villa->slug."-".$nr.".jpg")){
                $imgName = $villa->slug."-".$nr.".jpg";
                array_push($images, ['image' => $imgName]);
                $nr++;
             }
             if(!empty($images)){
                $villa->images()->createMany($images);
             }
        }
    }
    
}
