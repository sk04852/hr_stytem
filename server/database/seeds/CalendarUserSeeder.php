<?php

use App\Http\Controllers\CalendarUser\Models\CalendarUser;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;


class CalendarUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $calendarUserFaker = Faker::create('App\Http\Controllers\CalendarUser\Models\CalendarUser');
        for( $i=0; $i<=100; $i++) {
            $data = [
                "user_id" => $calendarUserFaker->numberBetween(1,50),
                "calendar_id"    => $calendarUserFaker->numberBetween(1,10),
                "invited_by"    => $calendarUserFaker->numberBetween(1,50)
            ];
            $calendarUser = new CalendarUser($data);
            $calendarUser->save();
        }
    }
}
