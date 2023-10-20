<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserprTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('user_pr', function (Blueprint $table) {
            $table->dropColumn([
                'timezone'
            ]);
            $table->unsignedBigInteger('timezone_id')->default(1)->after('password');
            $table->foreign('timezone_id')->references('id')->on('timezones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::table('user_pr', function (Blueprint $table) {
//            $table->dropForeign(['timezone_id']);
            $table->dropColumn([
                'timezone_id'
            ]);
            $table->string('timezone', 10);
        });
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }
}
