<?php

use App\Http\Controllers\Workflows\Models\Workflow;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class WorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Http\Controllers\Workflows\Models\Workflow');

        for($i=0 ; $i<=100; $i++) {
            $workflow = new Workflow();
            $workflow->module_id = 1;
            $workflow->description = $faker->text($maxNbChars = 20);
            $workflow->execution =  $faker->randomElement($array = array ('first save', 'first time condition', 'everytime saved', 'everytime modified', 'schedule'));
            $workflow->save();
        }
    }
}
