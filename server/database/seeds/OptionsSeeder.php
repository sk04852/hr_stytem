<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Option;

class OptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Models\Option');
        
        for($i=0 ; $i<=100; $i++) {

            $option = new Option();
            $option->name = $faker->name();
            $option->value=$faker->sentence($nbWords = 6, $variableNbWords = true);
            $option->autoload = 1;
           
            $option->save();
        
        }
    }
}
