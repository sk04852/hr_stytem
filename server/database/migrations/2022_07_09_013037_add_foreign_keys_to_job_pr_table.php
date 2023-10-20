<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToJobPrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_pr', function (Blueprint $table) {
            $table->foreignId('userpr_ID')->references('id')->on('user_pr');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_pr', function (Blueprint $table) {
            $table->dropForeign('userpr_ID');
        });
    }
}
