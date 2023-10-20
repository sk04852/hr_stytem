<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Http\Controllers\SMTPs\Models\SMTP;

class SMTPsSeeder extends Seeder
{
    public function run()
    {
        $Faker = Faker::create('App\Http\Controllers\SMTPs\Models\SMTP');
        //Using Loop for adding 1000 rows
        for ($i=0; $i <= 50; $i++) {
            $user_smtp = new SMTP();
            $user_smtp->module_id = $Faker->randomElement([7,24]);
            $user_smtp->relation_id = $Faker->numberBetween(1,10);
            $user_smtp->smtp_id = $Faker->numberBetween(1,10);
            $user_smtp->default = $Faker->randomElement(['Yes','No']);
            $user_smtp->save();
        }
    }
}
