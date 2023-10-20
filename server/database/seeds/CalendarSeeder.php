<?php

use App\Http\Controllers\Calendar\Models\Calendar;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CalendarSeeder extends Seeder
{
    public function run()
    {
        
        $calendarFaker = Faker::create('App\Http\Controllers\Calendar\Models\Calendar');
        $data = [
            "title" => "Google Calendar",
            "visibility"  => "Public",
            "source"  => "Google",
            "google_calendar_id"  => "primary",
            "created_by"    => 1
        ];
        $calendar = new Calendar($data);
        $calendar->save();
        $data = [
            "title" => "Workflow",
            "visibility"  => "Public",
            "source"  => "Default",
            "is_default" => "Yes",
            "created_by" => 1
        ];
        $calendar = new Calendar($data);
        $calendar->save();
      
    }
}
