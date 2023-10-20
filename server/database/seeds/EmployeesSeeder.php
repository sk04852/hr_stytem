<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\EmsEmployee\Models\Employee;
use Faker\Factory as Faker;

class EmployeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Http\Controllers\EmsEmployee\Models\Employee');

            $fleetDriver = new Employee();
            $fleetDriver->employee_number = 12345667;
            $fleetDriver->user_id = 1;
            $fleetDriver->save();
        
    }
}
