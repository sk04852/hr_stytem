<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alter5JobTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('videos');
        Schema::dropIfExists('job_activities');
        Schema::dropIfExists('job_activity');
        Schema::dropIfExists('job_shifts');
        Schema::dropIfExists('job_tag');
        Schema::dropIfExists('jobsfile');
        Schema::dropIfExists('job_work_lang');
        Schema::dropIfExists('job_desired_lang');
        Schema::dropIfExists('job_pr');
        Schema::dropIfExists('job');
        Schema::create('jobs_pr' , function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_pr_id');
            $table->unsignedTinyInteger('status');
            $table->date('deadline');
            $table->unsignedInteger('required_candidates');
            $table->string('creator', 255);
            $table->string('contact_name', 255);
            $table->string('contact_email', 255);
            $table->string('contact_number', 255);
            $table->boolean('training');
            $table->boolean('observation');
            $table->text('desired_language_comment')->nullable()->comment('if no desired language is select then comment will entered');
            $table->string('salary', 50);
            $table->string('salary_type', 50);
            $table->unsignedInteger('salary_amount_1');
            $table->unsignedInteger('salary_amount_2');
            $table->string('job_type', 50);
            $table->text('job_type_comment')->nullable();
            $table->string('transport', 50);
            $table->text('transport_comment')->nullable();
            $table->string('working_hours', 50);
            $table->text('working_hours_comment')->nullable();
            $table->string('clothes', 50);
            $table->text('clothes_comment')->nullable();
            $table->string('shifts', 50);
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });

        Schema::create('jobs' , function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('job_pr_id');
            $table->string('offer_name', 255);
            $table->string('title', 255);
            $table->text('benefits');
            $table->string('location', 255);
            $table->string('department', 255)->nullable();
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->text('comments')->nullable();
            $table->text('additional_information')->nullable();
            $table->string('recess', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('job_pr_id')->references('id')->on('jobs_pr');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });

        Schema::create('jobs_work_language' , function (Blueprint $table) {
            $table->unsignedBigInteger('job_pr_id');
            $table->unsignedBigInteger('language_id');
            $table->foreign('job_pr_id')->references('id')->on('jobs_pr');
            $table->foreign('language_id')->references('id')->on('languages');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });

        Schema::create('jobs_desired_language' , function (Blueprint $table) {
            $table->unsignedBigInteger('job_pr_id');
            $table->unsignedBigInteger('language_id');
            $table->foreign('job_pr_id')->references('id')->on('jobs_pr');
            $table->foreign('language_id')->references('id')->on('languages');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });

        Schema::create('jobs_shifts' , function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('job_pr_id');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('job_pr_id')->references('id')->on('jobs_pr');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });

        Schema::create('jobs_files' , function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('job_pr_id');
            $table->string('file_name', 50);
            $table->string('path', 255);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('job_pr_id')->references('id')->on('jobs_pr');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });

        Schema::create('jobs_videos' , function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('job_pr_id');
            $table->unsignedTinyInteger('type')->comment('1 means file, 2 means youtube');
            $table->string('file_name', 50)->nullable();
            $table->string('path', 255)->nullable();
            $table->string('link', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('job_pr_id')->references('id')->on('jobs_pr');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });

        Schema::enableForeignKeyConstraints();
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs_videos');
        Schema::dropIfExists('jobs_files');
        Schema::dropIfExists('jobs_shifts');
        Schema::dropIfExists('jobs_desired_language');
        Schema::dropIfExists('jobs_work_language');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('jobs_pr');
    }
}
