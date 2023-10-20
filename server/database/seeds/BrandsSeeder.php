<?php

use App\Http\Controllers\Brands\Models\Brand;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class BrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create(Brand::class);

        for($i=0 ; $i<=10; $i++) {

            $brand = new Brand();
            $brand->company_id = 1;
            $brand->status = 'Enable';
            $brand->name = $faker->word();
            $brand->primary_email = $faker->word();
            $brand->secondary_email = $faker->word();
            $brand->website = $faker->word();
            $brand->primary_phone = $faker->word();
            $brand->secondary_phone = $faker->word();
            $brand->fax = $faker->word();
            $brand->employees = 1;
            $brand->type = $faker->word();
            $brand->assigned_to = 1;
            $brand->created_by = 1;
            $brand->save();



        }
    }
}
