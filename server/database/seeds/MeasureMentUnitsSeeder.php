<?php

use App\Http\Controllers\Generics\Models\MeasurementUnit;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class MeasurementUnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Http\Controllers\Generics\Models\MeasurementUnit');
        $units = array('Coke Pallet','Sprite Pallet');
        for ($i=0 ; $i<2; $i++) {
            $unit = new MeasurementUnit();
            $unit->name = $units[$i];
            $unit->company_id = 1;
            $unit->param_1 = $faker->randomElement($array = array('12','6')); 
            $unit->save();
        }   
    }
}



