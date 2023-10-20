<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RestructuringTimelineAndApplyJobTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('timeline');
        Schema::dropIfExists('company_timeline');
        Schema::dropIfExists('candidate_history');
        Schema::create('candidatecv_jobs' , function (Blueprint $table) {
            $table->unsignedBigInteger('job_pr_id');
            $table->unsignedBigInteger('candidatecv_id');
            $table->unsignedBigInteger('action_id');
            $table->unsignedBigInteger('user_pr_id');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('job_pr_id')->references('id')->on('jobs_pr');
            $table->foreign('candidatecv_id')->references('id')->on('candidatecv');
            $table->foreign('action_id')->references('id')->on('actions');
            $table->foreign('user_pr_id')->references('id')->on('user_pr');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });

        Schema::create('timelines' , function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('model');
            $table->unsignedBigInteger('user_pr_id')->nullable()->comment('user who perform action');
            $table->string('title', 255);
            $table->string('action', 255);
            $table->string('old_values', 255)->nullable();
            $table->text('new_values')->nullable();
            $table->string('data', 255)->nullable();
            $table->text('additional_information')->nullable();
            $table->timestamps();
            $table->foreign('user_pr_id')->references('id')->on('user_pr');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });

        Schema::create('timelines_comments' , function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parent_id')->nullable()->default(null);
            $table->unsignedBigInteger('timeline_id');
            $table->unsignedBigInteger('user_pr_id')->comment('user who perform action');
            $table->text('body');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('parent_id')->references('id')->on('timelines_comments');
            $table->foreign('timeline_id')->references('id')->on('timelines');
            $table->foreign('user_pr_id')->references('id')->on('user_pr');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timelines_comments');
        Schema::dropIfExists('timelines');
        Schema::dropIfExists('candidatecv_jobs');
    }
}
