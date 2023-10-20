<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if(Schema::hasTable('smtp_connections')){
           $mail = DB::table('smtp_connections')->select(['*'])->where('status', 'Active')->first();
                if ($mail) {
                    $config = array(

                        'driver'     => 'smtp',
                        'host'       => $mail->mail_host,
                        'port'       => $mail->port,
                        'from'       => array('address' => is_null($mail->from_email) ? 'info@pos.com' : $mail->from_email , 'name' => is_null($mail->from_name) ? 'Surprisy' : $mail->from_name),
                        'encryption' => null,
                        'username'   => $mail->user_name,
                        'password'   => $mail->password,
                        'sendmail'   => '/usr/sbin/sendmail -bs',
                        'pretend'    => false,
                    );
                    Config::set('mail', $config);
                }
            }
        }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
       
           
        
    }
}
