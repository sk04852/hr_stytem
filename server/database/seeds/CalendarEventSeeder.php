<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Http\Controllers\CalendarEvents\Models\CalendarEvent;

class CalendarEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $Faker = Faker::create('App\Http\Controllers\CalendarEvents\Models\CalendarEvent');
        //Using Loop for adding 1000 rows
        for ($i=0; $i <= 50; $i++) {
            $calendar_event = new CalendarEvent();
            $calendar_event->calendar_id = $Faker->numberBetween(1,10);
            $calendar_event->subject = $Faker->text($maxNbChars = 50);
            $calendar_event->type = $Faker->randomElement(['Todo','Event']);
            $calendar_event->activity_type = $Faker->randomElement(['Meeting', 'Call', 'Mobile Call']);
            $calendar_event->start_date_time = $Faker->randomElement(['2020-09-16 9:26:21', '2020-09-16 15:15:00', '2020-09-18 14:26:21']);
            $calendar_event->end_date_time = $Faker->randomElement(['2020-09-20 14:26:21', '2020-09-22 14:26:21', '2020-09-19 14:26:21']);
            $calendar_event->visibility = $Faker->randomElement(['Private', 'Public' ]);
            $calendar_event->notification = $Faker->boolean();
            $calendar_event->status = $Faker->randomElement(['Held', 'Not Held', 'Planned']);
            $calendar_event->priority = $Faker->randomElement(['High', 'Medium', 'Low']);
            $calendar_event->assigned_to = $Faker->randomDigitNot(0); 
            $calendar_event->created_by = $Faker->randomDigitNot(0);
            $calendar_event->location = $Faker->text($maxNbChars = 10);
            $calendar_event->description = $Faker->text($maxNbChars = 40);
            $calendar_event->save();
        }
    }
}
