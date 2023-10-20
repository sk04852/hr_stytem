<?php

use App\Http\Controllers\Notifications\Models\Notification;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class NotificationsSeeder extends Seeder
{
    public function run()
    {
        $Faker = Faker::create('App\Http\Controllers\Notifications\Models\Notification');
        for ($i=0; $i <= 50; $i++) {
            $notification = new Notification();
            $notification->body = $Faker->text($maxNbChars = 100);
            $notification->payload = null;
            $notification->type = 917;
            $notification->save();
        }
    }
}
