<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alter3GoogleCalendarTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_calendars', function (Blueprint $table){
            $table->string('color', 255)->nullable()->default(null)->after('title');
            $table->string('timezone', 255)->nullable()->default(null)->after('is_default');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('google_calendars', function (Blueprint $table){
            $table->dropColumn([
                'timezone',
                'color'
            ]);
        });
    }
}
