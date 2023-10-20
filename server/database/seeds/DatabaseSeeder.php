<?php

use Database\Seeders\LeadSeeder;
use Database\Seeders\TicketSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            // Order of these classes is important, add new ones at the end.
            DefaultValuesSeeder::class,
            CompaniesSeeder::class,
            DefaultSmtpSeeder::class,
            ClientSeeder::class,
            UserSeeder::class,
            AdminAccountSeeder::class,
            BrandsSeeder::class,
            FieldOptionTypeSeeder::class,
            CalendarSeeder::class,
            NotificationsSeeder::class,
            Database\Seeders\LeadsSeeder::class,
            TicketSeeder::class,
        ]);
        Artisan::call('sql:migrate', []);
    }
}
