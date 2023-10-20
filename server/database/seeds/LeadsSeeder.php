<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Http\Controllers\Leads\Models\Lead;

class LeadsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $faker = Faker::create('App\Http\Controller\Leads\Models\Lead');
        for ($i = 0; $i<10; $i++) {
            $lead = new Lead();
            $lead->first_name = $faker->firstName();
            $lead->last_name = $faker->lastName();
            $lead->lead_status = $faker->numberBetween(114, 122);
            $lead->created_by = 1;
            $lead->save();
        }
    }
}
