<?php

namespace Database\Seeders;

use App\Http\Controllers\Tickets\Models\Ticket;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Http\Controllers\Tickets\Models\Ticket');
        for ($i=0; $i < 1000; $i++) {
            $ticket = new Ticket();
            $ticket->title = $faker->word();
            $ticket->status = $faker->numberBetween(95, 99);
            $ticket->assigned_to = 1;
            $ticket->created_by = 1;
            $ticket->save();
        }
    }
}
