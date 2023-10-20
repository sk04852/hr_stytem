<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\MediaType;

class MediaTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Models\MediaType');
        
        for($i=0 ; $i<=100; $i++) {

            $media_type = new MediaType();
            $media_type->name = $faker->name();
            $media_type->short_name = $faker->name();
            $media_type->description = $faker->text($maxNbChars = 50);
            $media_type->sort_order = $faker->randomDigit(); 
            $media_type->save();
        
        }
    }
}
