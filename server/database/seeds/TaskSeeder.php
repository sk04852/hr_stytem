<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Tasks\Models\Task;
use Faker\Factory as Faker;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Http\Controllers\Tasks\Models');

        for($i=0 ; $i<=100; $i++) {
            $task = new Task();
            $task->name = $faker->name();
            $task->assigned_to = $faker->lastName();
            $task->assigned_to_id = 1;
            $task->start_date_time = $faker->dateTimeBetween('+1 week', '+1 month');
            $task->due_date_time = $faker->dateTimeBetween('+2 week', '+2 month');
            $task->related_to = $faker->randomElement($array = array ("Organizations", "Campaigns","Cases","Internal Transfer","Invoices","Deals","Leads"));
            $task->related_to_id = $faker->randomDigit();
            $task->stage = $faker->randomElement($array = array ("Pending", "In Progress","Completed","Skipped","Cancelled"));
            $task->priority = $faker->randomElement($array = array ("High", "Medium","Low"));
            $task->location = $faker->address();
            $task->previous_task = $faker->randomDigit();
            $task->parent_task = $faker->randomDigit();
            $task->task_type = $faker->randomElement($array = array ("Sale", "Lead","Training","General","Reminder"));
            $task->time_estimation_in_mins = $faker->randomDigit();
            $task->time_spent_in_mins = $faker->randomDigit();
            $task->related_task = $faker->randomDigit();
            $task->description = $faker->sentence($nbWords = 6, $variableNbWords = true);
            $task->created_by = 1;
            $task->save();
        }
    }  

}
