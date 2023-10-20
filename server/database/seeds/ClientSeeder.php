<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Clients\Models\Client;
use Faker\Factory as Faker;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Http\Controllers\Clients\Models\Client');

        for($i=0 ; $i<=5; $i++) {
        
            $client = new Client();
            $password = $faker->password();
            $username = Str::of($faker->userName())->limit(6);
            $phone = Str::of($faker->phoneNumber())->limit(12);
            $mobile = Str::of($faker->phoneNumber())->limit(12);
            $client->first_name = $faker->firstName();
            $client->middle_name = $faker->firstNameMale();
            $client->last_name = $faker->lastName();
            $client->username = $username;
            $client->email = $faker->email();
            $client->password = $password;
            $client->phone = $phone;
            $client->mobile = $mobile;
            $client->address = $faker->address();
            $client->city = $faker->city();
            $client->state = $faker->state();
            $client->zip = $faker->postcode();
            $client->company_id = 1;
            $client->currency = 1;
            $client->save();
        }
            
    }
}
