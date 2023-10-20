<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterGoogleCalendarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_calendars', function (Blueprint $table) {
            $table->boolean('is_checked')->nullable()->default(1)->after('is_default');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('google_calendars', function (Blueprint $table) {
            $table->dropColumn([
                'is_checked'
            ]);
        });
    }
}
