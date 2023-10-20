<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Http\Controllers\Users\Models\UserExtraInfo;

class UserMoreInformationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Http\Controllers\Users\Models\UserExtraInfo');
        
        for($i=0 ; $i<=10; $i++) {
            
            $user_extra_info = new UserExtraInfo();
            $user_extra_info->user_id = 1;
            $user_extra_info->title = $faker->sentence($nbWords = 6, $variableNbWords = true);
            $user_extra_info->fax = $faker->randomNumber();
            $user_extra_info->department = $faker->country();;
            $user_extra_info->other_email = $faker->email();;
            $user_extra_info->office_phone = $faker->randomNumber();
            $user_extra_info->mobile_phone = $faker->e164PhoneNumber();
            $user_extra_info->home_phone = $faker->randomNumber();
            $user_extra_info->save();
        
        }
    }
}
