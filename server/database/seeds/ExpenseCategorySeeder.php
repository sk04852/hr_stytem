<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Expenses\Models\ExpenseCategory;
use Faker\Factory as Faker;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Http\Controllers\Expenses\Models\ExpenseCategory');

        for($i=0 ; $i<=10; $i++) {
        
            $expense = new ExpenseCategory();
            $expense->name = $faker->word();
            $expense->created_by = 1;
            $expense->save();
        }
    }
}
