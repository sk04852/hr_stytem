<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidatecvSkillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidatecv_skill', function (Blueprint $table) {
            $table->unsignedBigInteger('candidatecv_id');
            $table->unsignedBigInteger('skill_id');
            $table->timestamps();
            $table->foreign('candidatecv_id')->references('id')->on('candidatecv');
            $table->foreign('skill_id')->references('id')->on('skills');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidatecs_skill');
    }
}
