<?php

use App\Http\Controllers\Generics\Models\Category;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Http\Controllers\Generics\Models\Category');
        $units = array('Electricity','Refreshment');
        for ($i=0 ; $i<2; $i++) {
            $unit = new Category();
            $unit->name = $units[$i];
            $unit->company_id = 1;
            $unit->param_1 = $faker->randomElement($array = array('red','green')); 
            $unit->save();
        }   
    }
}



