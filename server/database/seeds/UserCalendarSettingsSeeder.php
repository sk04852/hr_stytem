<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Http\Controllers\Users\Models\UserCalendarSetting;

class UserCalendarSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Http\Controllers\Users\Models\UserCalendarSetting');
        $timeSlots = ['12:00 AM','1:00 AM', '2:00 AM','3:00 AM','4:00 AM','5:00 AM','6:00 AM','7:00 AM','8:00 AM','9:00 AM','10:00 AM','11:00 AM','12:00 PM','1:00 PM','2:00 PM','3:00 PM', '4:00 PM', '5:00 PM', '6:00 PM', '7:00 PM', '8:00 PM', '9:00 PM', '10:00 PM', '11:00 PM'];
        $dateFormat = ['dd-mm-yyyy', 'mm-dd-yyyy', 'yyyy-mm-dd'];
 
        for($i=0 ; $i<=10; $i++) {
            
            $user_calendar_setting = new UserCalendarSetting();
            $user_calendar_setting->user_id = 1;
            $user_calendar_setting->day_starts_at = $timeSlots[rand(0,23)];
            $user_calendar_setting->date_format = $dateFormat[rand(0,2)];
          //  $user_calendar_setting->calendar_hour_format = $faker->randomElement(12, 24);
            $user_calendar_setting->save();
        
        }
    }
}
