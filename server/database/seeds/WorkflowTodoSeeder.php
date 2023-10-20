<?php

use App\Http\Controllers\Workflows\Models\WorkflowTodo;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class WorkflowTodoSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('App\Http\Controllers\Workflows\Models\WorkflowTodo');

        for($i=0 ; $i<=10; $i++) {
            $workflow_todo = new WorkflowTodo();
            $workflow_todo->workflow_id = 1;
            $workflow_todo->task_title = "Title";
            $workflow_todo->todo = $faker->randomElement($array = array ('Send mail', 'Invoke custom function', 'Create todo', 'Create event', 'Update fields', 'Create entity'));
            $workflow_todo->save();
        }
    }
}
