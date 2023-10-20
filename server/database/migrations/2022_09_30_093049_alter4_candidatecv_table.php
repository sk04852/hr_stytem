<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alter4CandidatecvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidatecv', function (Blueprint $table) {
            $table->unsignedSmallInteger("status")->default(0)->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidatecv', function (Blueprint $table) {
            $table->dropColumn([
                'status'
            ]);
        });
    }
}
