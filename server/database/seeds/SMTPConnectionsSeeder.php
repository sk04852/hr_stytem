<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Http\Controllers\SMTPConnections\Models\SMTPConnection;

class SMTPConnectionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $smtpConnection = new SMTPConnection();
        $smtpConnection->title = "CRM Default Connection";
        $smtpConnection->server_name = "smtp.eu.mailgun.org";
        $smtpConnection->port = 587;
        $smtpConnection->user_name = "crm@digibits.xyz";
        $smtpConnection->password = "d65a0de3409b03d91a7aa4d2852f294a-53c13666-5dcf2fae";
        $smtpConnection->from_email = "no-reply@crm.digibits.xyz";
        $smtpConnection->status =  "Active";
        $smtpConnection->save();
    }
}
