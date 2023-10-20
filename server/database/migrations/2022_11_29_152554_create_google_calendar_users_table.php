<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleCalendarUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_calendar_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_pr_id');
            $table->unsignedBigInteger('google_calendar_id');
            $table->unsignedBigInteger('invited_by_id')->comment('this is also user_pr');
            $table->string('rule_id', 255);
            $table->timestamps();
            $table->foreign('user_pr_id')->references('id')->on('user_pr');
            $table->foreign('google_calendar_id')->references('id')->on('google_calendars');
            $table->foreign('invited_by_id')->references('id')->on('user_pr');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('google_calendar_users');
    }
}
