<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alter9JobPrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs_pr', function(Blueprint $table){
            $table->unsignedBigInteger('auto_generate_url_id')->nullable()->default(null)->after('shifts');

            $table->foreign('auto_generate_url_id')->references('id')->on('auto_generate_urls');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jobs_pr', function(Blueprint $table){
            $table->dropForeign([
                'auto_generate_url_id'
            ]);

            $table->dropColumn([
                'auto_generate_url_id'
            ]);
        });
    }
}
