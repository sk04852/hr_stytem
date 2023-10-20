<?php

use Illuminate\Database\Seeder;
use App\Models\Language;
use Faker\Factory as Faker;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Models\Language');

        for($i=0 ; $i<=10; $i++) {
        
            $languges = new Language();
            $languges->name = $faker->name();
            $languges->prefix = $faker->randomLetter();
            $languges->save();
        }
    }
}
