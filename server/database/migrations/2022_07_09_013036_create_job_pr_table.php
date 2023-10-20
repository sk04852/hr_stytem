<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobPrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_pr', function (Blueprint $table) {
            $table->id();
            $table->integer('jobpr_ID');
            $table->integer('job_ID');
            $table->smallInteger('workers');
            $table->integer('companypr_ID');
            $table->integer('userpr_ID')->index('userpr_ID');
            $table->date('deadline');
            $table->string('site', 500);
            $table->float('salary', 10, 0);
            $table->float('bonus', 10, 0);
            $table->float('optional', 10, 0);
            $table->date('date_created');
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
        Schema::dropIfExists('job_pr');
    }
}
