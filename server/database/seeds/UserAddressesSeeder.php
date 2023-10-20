<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Http\Controllers\Users\Models\UserAddress;

class UserAddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Http\Controllers\Users\Models\UserAddress');
        
        for($i=0 ; $i<=10; $i++) {
            
            $user_address = new UserAddress();
            $user_address->user_id = 1;
            $user_address->street_address = $faker->streetAddress();
            $user_address->city = $faker->city();
            $user_address->state = $faker->state();
            $user_address->country = $faker->country();
            $user_address->save();
        
        }
    }
}
