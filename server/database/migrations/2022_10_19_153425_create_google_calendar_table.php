<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleCalendarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('user_pr', function (Blueprint $table) {
            $table->text('google_calendar_access_token')->nullable()->after('skype_personal_link');
            $table->text('google_calendar_refresh_token')->nullable()->after('google_calendar_access_token');
            $table->text('google_calendar_user_account_info')->nullable()->after('google_calendar_refresh_token');
        });
        Schema::create('google_calendar_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_pr_id');
            $table->string('start', 255);
            $table->string('end', 255);
            $table->integer('status')->nullable()->default(0);
            $table->integer('is_all_day')->nullable()->default(0);
            $table->string('title', 255);
            $table->text('description');
            $table->string('google_event_id', 255);
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
        Schema::table('user_pr', function (Blueprint $table) {
            $table->dropColumn([
                'google_calendar_access_token',
                'google_calendar_refresh_token',
                'google_calendar_user_account_info'
            ]);
        });
        Schema::dropIfExists('google_calendar_events');
    }
}
