<?php

use Illuminate\Database\Seeder;
use App\Models\GeneralSetting;
use Faker\Factory as Faker;

class GeneralSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Models\GeneralSetting');

        for($i=0 ; $i<=100; $i++) {
        
            $general_setting = new GeneralSetting();
            $general_setting->type = $faker->word();
            $general_setting->name = $faker->name();
            $general_setting->value = $faker->word();
            $general_setting->description = $faker->sentence($nbWords = 6, $variableNbWords = true);
            $general_setting->autoload = random_int(0, 1);
            $general_setting->save();
        }
    }
}
