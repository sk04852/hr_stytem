<?php

use App\Http\Controllers\SMTPConnections\Models\SMTPConnection;
use Illuminate\Database\Seeder;

class DefaultSmtpSeeder extends Seeder
{
    // this is my smtp detail when you will use this you should change your detail
    public function run()
    {
        $DefaultConnection = new SMTPConnection();
        $DefaultConnection->title = 'Mail Trap';
        $DefaultConnection->server_name = 'Mail Trap';
        $DefaultConnection->mail_host = 'smtp.mailtrap.io';
        $DefaultConnection->port = 2525;
        $DefaultConnection->company_id = 1;
        $DefaultConnection->user_name = '66470ebf7c3a47';
        $DefaultConnection->password = '30c9b486f9af2e';
        $DefaultConnection->requires_authentication = 1;
        $DefaultConnection->status = 'Active';
        $DefaultConnection->save();
    }
}
