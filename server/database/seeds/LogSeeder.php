<?php

use Illuminate\Database\Seeder;
use App\Models\Log;
use Faker\Factory as Faker;

class LogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Models\Log');
   
           for($i=0 ; $i<=100; $i++) {
           
               $log = new Log();
               $log->user_id = 1;
               $log->data = $faker->sentence($nbWords = 6, $variableNbWords = true);
               $log->save();
        
           }
    }       
}

