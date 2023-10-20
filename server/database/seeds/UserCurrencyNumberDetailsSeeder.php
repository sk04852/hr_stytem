<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Http\Controllers\Users\Models\UserCurrencyNumberDetails;

class UserCurrencyNumberDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Http\Controllers\Users\Models\UserCurrencyNumberDetails');
        $digitGroupingPattern = ['123,456,789', '123456789', '123456,789', '12,34,56,789'];
        
        for($i=0 ; $i<=10; $i++) {
            $user_currency_number_details = new UserCurrencyNumberDetails();
            $user_currency_number_details->user_id = 1;
            $user_currency_number_details->currency = $faker->randomElement(array('USA, Dollars','EUR, Euro'));
            $user_currency_number_details->decimal_separator = $faker->randomElement(array('.', ',', '\'', 'Space', '$'));
            $user_currency_number_details->symbol_placement = $faker->randomElement(array('$1.0', '1.0$'));
            $user_currency_number_details->digit_grouping_pattern = $digitGroupingPattern[rand(0,3)];
            $user_currency_number_details->save();
        }
    }  
}
