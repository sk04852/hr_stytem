<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alter7CandidatecvTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('education_levels', function(Blueprint $table){
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });

        Schema::table('candidatecv_education', function(Blueprint $table){
            $table->unsignedBigInteger('degree_id')->nullable()->default(null)->change();
            $table->unsignedBigInteger('level_id')->nullable()->default(null)->after('degree_id');
            $table->string('speciality', 255)->nullable()->default(null)->change();

            $table->dropColumn([
                'starting_date',
                'ending_date'
            ]);

            $table->string('starting_year', 4)->after('speciality');
            $table->string('starting_month', 2)->nullable()->default(null)->after('starting_year');
            $table->string('starting_day', 2)->nullable()->default(null)->after('starting_month');
            $table->string('ending_year', 4)->after('starting_day');
            $table->string('ending_month', 2)->nullable()->default(null)->after('ending_year');
            $table->string('ending_day', 2)->nullable()->default(null)->after('ending_month');
            $table->string('additonal_information', 255)->nullable()->default(null)->after('ending_day');

            $table->foreign('level_id')->references('id')->on('education_levels');
        });


        Schema::table('candidatecv_job_history', function(Blueprint $table){
            $table->dropColumn([
                'starting_date',
                'ending_date'
            ]);

            $table->string('starting_year', 4)->after('designation');
            $table->string('starting_month', 2)->nullable()->default(null)->after('starting_year');
            $table->string('starting_day', 2)->nullable()->default(null)->after('starting_month');
            $table->string('ending_year', 4)->after('starting_day');
            $table->string('ending_month', 2)->nullable()->default(null)->after('ending_year');
            $table->string('ending_day', 2)->nullable()->default(null)->after('ending_month');
        });

        Schema::table('candidatecv_additional_courses', function(Blueprint $table){
            $table->string('title', 255)->nullable()->default(null)->change();
            $table->dropColumn([
                'start_date',
                'end_date'
            ]);

            $table->string('starting_year', 4)->after('description');
            $table->string('starting_month', 2)->nullable()->default(null)->after('starting_year');
            $table->string('starting_day', 2)->nullable()->default(null)->after('starting_month');
            $table->string('ending_year', 4)->after('starting_day');
            $table->string('ending_month', 2)->nullable()->default(null)->after('ending_year');
            $table->string('ending_day', 2)->nullable()->default(null)->after('ending_month');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidatecv_additional_courses', function(Blueprint $table){
            $table->string('title', 255)->nullable(false)->change();

            $table->dropColumn([
                'ending_day',
                'ending_month',
                'ending_year',
                'starting_day',
                'starting_month',
                'starting_year'
            ]);

            $table->date('start_date')->after('description');
            $table->date('end_date')->after('start_date');            
        });

        Schema::table('candidatecv_job_history', function(Blueprint $table){
            $table->dropColumn([
                'ending_day',
                'ending_month',
                'ending_year',
                'starting_day',
                'starting_month',
                'starting_year'
            ]);

            $table->date('starting_date')->after('designation');
            $table->date('ending_date')->after('starting_date');
        });


        Schema::table('candidatecv_education', function(Blueprint $table){
            

            $table->unsignedBigInteger('degree_id')->nullable(false)->change();

            $table->dropForeign([
                'level_id'
            ]);

            $table->dropColumn([
                'level_id'
            ]);

            
            $table->string('speciality', 255)->nullable(false)->change();

            $table->dropColumn([
                'additonal_information',
                'ending_day',
                'ending_month',
                'ending_year',
                'starting_day',
                'starting_month',
                'starting_year'
            ]);

            $table->date('starting_date')->after('speciality');
            $table->date('ending_date')->after('starting_date');
        });


        Schema::dropIfExists('education_levels');
    }
}
