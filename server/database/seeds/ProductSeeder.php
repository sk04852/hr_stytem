<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Products\Models\Product;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Http\Controllers\Products');
        
        for ($i=0 ; $i<=50; $i++) {
        }   
    }
}
