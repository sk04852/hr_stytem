<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterGoogleCalendarUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_calendar_users', function (Blueprint $table) {
            $table->string('role', 50)->nullable()->default(null)->after('invited_by_id');
        });

        Schema::table('google_calendar_events', function (Blueprint $table) {
            $table->string('transparency', 50)->nullable()->default(null)->after('description');
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
            $table->dropColumn([
                'transparency'
            ]);
        });

        Schema::table('google_calendar_users', function (Blueprint $table) {
            $table->dropColumn([
                'role'
            ]);
        });
    }
}
