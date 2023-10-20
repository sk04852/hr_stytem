<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_calendars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_pr_id');
            $table->string('title', 255);
            $table->string('visibility', 255);
            $table->string('source', 255)->nullable();
            $table->unsignedSmallInteger('is_default');
            $table->string('google_calendar_id', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_pr_id')->references('id')->on('user_pr');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('google_calendars');
    }
}
