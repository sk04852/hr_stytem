<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alter6JobTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs_pr', function (Blueprint $table){
            $table->string('creator', 255)->nullable()->default(null)->change();
            $table->unsignedInteger('salary_amount_2')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jobs_pr', function (Blueprint $table){
            $table->string('creator', 255)->nullable(false)->change();
            $table->unsignedInteger('salary_amount_2')->nullable(false)->change();
        });
    }
}
