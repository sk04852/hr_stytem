<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alter8CandidatecvTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidatecv_education', function(Blueprint $table){
            $table->string('ending_year', 4)->nullable()->default(null)->change();
        });


        Schema::table('candidatecv_job_history', function(Blueprint $table){
            $table->string('ending_year', 4)->nullable()->default(null)->change();
        });

        Schema::table('candidatecv_additional_courses', function(Blueprint $table){
            $table->string('ending_year', 4)->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidatecv_education', function(Blueprint $table){
            $table->string('ending_year', 4)->nullable(false)->change();
        });


        Schema::table('candidatecv_job_history', function(Blueprint $table){
            $table->string('ending_year', 4)->nullable(false)->change();
        });

        Schema::table('candidatecv_additional_courses', function(Blueprint $table){
            $table->string('ending_year', 4)->nullable(false)->change();
        });
    }
}
