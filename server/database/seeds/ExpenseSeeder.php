<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Expenses\Models\Expense;
use Faker\Factory as Faker;


class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Http\Controllers\Expenses\Models\Expense');
        $status = ['Open', 'Processing', 'Approved', 'Reimbursed', 'Close','Deleted'];

        for($i=0 ; $i<=10; $i++) {
        
            $expense = new Expense();
            $expense->merchant = $faker->firstName();
            $expense->date = $faker->dateTime();
            $expense->amount = $faker->randomDigit();
            $expense->currency = $faker->word();
            $expense->category_id = 1; 
            $expense->created_by_id = 1; 
            $expense->reporter_id = 1; 
            $expense->company_id = 1; 
            $expense->currency = $faker->randomElement($status);
            $expense->currency = $faker->word();
            $expense->reimbursable = 1;
            $expense->save();
        }
    }
}
