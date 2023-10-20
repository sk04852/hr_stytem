<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alter2GoogleCalendarEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_calendar_events', function (Blueprint $table) {
            $table->string('title', 255)->nullable()->default(null)->change();
            $table->text('description')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('google_calendar_events', function (Blueprint $table) {
            $table->string('title', 255)->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
        });
    }
}
