<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alter2UserPrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_pr', function (Blueprint $table) {
            $table->string('zoom_id')->nullable()->default(null)->after('password');
            $table->string('zoom_personal_link')->nullable()->default(null)->after('zoom_id');
            $table->string('skype_id')->nullable()->default(null)->after('zoom_personal_link');
            $table->string('skype_personal_link')->nullable()->default(null)->after('skype_id');
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
                'skype_personal_link',
                'skype_id',
                'zoom_personal_link',
                'zoom_id'
            ]);
        });
    }
}
