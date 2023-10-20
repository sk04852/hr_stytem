<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Faker\Provider\en_AU\Address;

class GenericAreasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $area = Faker::create('App\Http\Controllers\Generics\Models\Area');
        $area->addProvider(new Address($area));

        for($i=0 ; $i<=100; $i++) {
            $data = [
                'name' => $area->city,
                'param_1'  => $area->postcode,
                'param_2'  => $area->country,
            ];
            $areaModel = new App\Http\Controllers\Generics\Models\Area();
            $areaModel->fill($data);
            $areaModel->save();
        }
    }
}
