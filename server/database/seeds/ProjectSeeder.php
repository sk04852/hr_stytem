<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

use App\Http\Controllers\Project\Models\Project;


class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Http\Controllers\Project\Models\Project');

        $project = new Project();
        $project->project_name = $faker->word();
        $project->save();
    }
}
