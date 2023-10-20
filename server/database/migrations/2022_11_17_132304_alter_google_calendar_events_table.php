<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterGoogleCalendarEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_calendar_events', function (Blueprint $table) {
            $table->unsignedBigInteger('google_calendar_id')->nullable()->default(null)->after('id');
            $table->string('subject', 255)->nullable()->default(null)->after('user_pr_id');
            $table->string('type', 255)->nullable()->default(null)->after('subject');
            $table->string('activity_type', 255)->nullable()->default(null)->after('type');
            $table->string('visibility', 255)->nullable()->default(null)->after('activity_type');
            $table->string('notification', 255)->nullable()->default(null)->after('visibility');
            $table->string('priority', 255)->nullable()->default(null)->after('notification');
            $table->string('assigned_to', 255)->nullable()->default(null)->after('priority');
            $table->string('location', 255)->nullable()->default(null)->after('assigned_to');
            $table->foreign('google_calendar_id')->references('id')->on('google_calendars');
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
            $table->dropForeign([
                'google_calendar_id'
            ]);
            $table->dropColumn([
                'location',
                'assigned_to',
                'priority',
                'notification',
                'visibility',
                'activity_type',
                'type',
                'subject',
                'google_calendar_id'
            ]);
        });
    }
}
