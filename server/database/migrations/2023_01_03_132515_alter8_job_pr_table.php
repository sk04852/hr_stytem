<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alter8JobPrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs_pr', function(Blueprint $table){
            $table->date('deadline')->nullable()->default(null)->change();
            $table->string('desired_start_time')->nullable()->default(null)->after('deadline');
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
            $table->dropColumn([
                'desired_start_time'
            ]);

            $table->date('deadline')->nullable(false)->change();
        });
    }
}
