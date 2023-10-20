<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alter2CandidatecvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name', 255);
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });

        Schema::create('nationalities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });

        // keywords conflicts with Users tags
//        Schema::create('keywords', function (Blueprint $table) {
//            $table->id();
//            $table->string('name');
//            $table->timestamps();
//            $table->softDeletes();
//            $table->engine = 'InnoDB';
//            $table->charset = 'utf8';
//            $table->collation = 'utf8_general_ci';
//        });


        Schema::table('candidatecv', function (Blueprint $table) {
            $table->string('personal_information', 255)->nullable()->default(null)->after('location');
            $table->string('personal_code', 255)->nullable()->default(null)->after('personal_information');
            $table->unsignedSmallInteger('children_qty')->nullable()->default(null)->after('personal_code');
            $table->string('children_names', 500)->nullable()->default(null)->after('children_qty');
            $table->string('marital_status', 50)->nullable()->default(null)->after('children_names');
//            $table->string('desired_job_type', 255)->nullable()->default(null)->after('marital_status'); //Conflict with already exist name job type
            $table->string('desired_job', 255)->nullable()->default(null)->after('marital_status');
            $table->unsignedMediumInteger('desired_salary')->nullable()->default(null)->after('desired_job');
            $table->string('desired_job_time', 255)->nullable()->default(null)->after('desired_salary');
            $table->string('desired_job_location', 255)->nullable()->default(null)->after('desired_job_time');
//            $table->text('tk_description')->nullable()->default(null)->after('desired_work_location')->comment('TK means additional');
//            $table->string('tk_name', 255)->nullable()->default(null)->after('tk_description');
            $table->unsignedSmallInteger('age')->nullable()->default(null)->after('desired_job_location');
            $table->text('keywords')->nullable()->default(null)->after('age');
//            $table->unsignedBigInteger('nationality_id')->nullable()->default(null)->after('keywords');
//            $table->foreign('nationality_id')->references('id')->on('nationalities');
        });


        Schema::table('candidatecv_job_history', function (Blueprint $table) {
            $table->text('description')->nullable()->default(null)->after('company_name');
            $table->string('work_place', 255)->nullable()->default(null)->after('description');
        });

        Schema::table('candidatecv_education', function (Blueprint $table) {
            $table->string('speciality', 255)->after('institute');
            $table->boolean('still_studying')->nullable()->default(null)->after('ending_date');
        });

        Schema::create('candidatecv_additional_courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidatecv_id');
            $table->string('title', 255);
            $table->string('description', 255)->nullable()->default(null);
            $table->date('start_date')->nullable()->default(null);
            $table->date('end_date')->nullable()->default(null);
            $table->unsignedSmallInteger('total_hours')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->foreign('candidatecv_id')->references('id')->on('candidatecv');
        });

        Schema::create('candidatecv_driving_licenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidatecv_id');
            $table->string('level');
            $table->date('issue_date')->nullable()->default(null);
            $table->date('expiry_date')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->foreign('candidatecv_id')->references('id')->on('candidatecv');
        });

        Schema::create('candidatecv_recommenders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidatecv_id');
            $table->text('recommendation');
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->foreign('candidatecv_id')->references('id')->on('candidatecv');
        });

//        Schema::create('candidatecv_keyword', function (Blueprint $table) {
//            $table->unsignedBigInteger('candidatecv_id');
//            $table->unsignedBigInteger('keyword_id');
//            $table->foreign('candidatecv_id')->references('id')->on('candidatecv');
//            $table->foreign('keyword_id')->references('id')->on('keywords');
//        });

        Schema::create('candidatecv_nationality', function (Blueprint $table) {
            $table->unsignedBigInteger('candidatecv_id');
            $table->unsignedBigInteger('nationality_id');
            $table->foreign('candidatecv_id')->references('id')->on('candidatecv');
            $table->foreign('nationality_id')->references('id')->on('nationalities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('candidatecv_nationality');
        Schema::drop('candidatecv_recommenders');
        Schema::drop('candidatecv_driving_licenses');
        Schema::drop('candidatecv_additional_courses');

        Schema::table('candidatecv_education', function (Blueprint $table){
           $table->dropColumn([
               'still_studying',
               'speciality'
           ]);
        });

        Schema::table('candidatecv_job_history', function (Blueprint $table){
            $table->dropColumn([
                'work_place',
                'description'
            ]);
        });

        Schema::table('candidatecv', function (Blueprint $table){
//            $table->dropForeign([
//                'nationality_id'
//            ]);
            $table->dropColumn([
//                'nationality_id',
                'keywords',
                'age',
                'desired_job_location',
                'desired_job_time',
                'desired_salary',
                'desired_job',
                'marital_status',
                'children_names',
                'children_qty',
                'personal_code',
                'personal_information'
            ]);
        });

        Schema::drop('nationalities');
        Schema::drop('countries');
    }
}
