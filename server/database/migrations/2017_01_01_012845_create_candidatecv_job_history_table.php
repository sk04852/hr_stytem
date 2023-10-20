<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidatecvJobHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('candidatecv_job_history');
        Schema::create('candidatecv_job_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidatecv_id');
            $table->string('company_name', 100);
            $table->string('designation', 50);
            $table->date('starting_date');
            $table->date('ending_date');
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->foreign('candidatecv_id')->references('id')->on('candidatecv');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidatecv_job_history');
    }
}
