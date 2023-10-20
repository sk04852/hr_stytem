<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidatecvPrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidatecv_pr', function (Blueprint $table) {
            $table->id();
            $table->integer('candidate_ID');
            $table->string('int_code', 5);
            $table->string('email', 50);
            $table->string('phone_number', 20);
            $table->binary('photo');
            $table->date('date_added');
            $table->integer('userpr_ID');
            $table->smallInteger('nr_of_jobs');
            $table->timestamp('time_from_last_act')->useCurrentOnUpdate()->useCurrent();
            $table->date('date_of_birth');
            $table->string('lang_lvl', 2);
            $table->integer('file_ID');
            $table->integer('tag_ID');
            $table->integer('skill_ID');
            $table->integer('action_ID');
            $table->integer('consent_ID');
            $table->timestamps();
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
        Schema::dropIfExists('candidatecv_pr');
    }
}
