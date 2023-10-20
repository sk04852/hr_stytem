<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AgrelloTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agrello_responses', function (Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('candidatecv_id');
            $table->unsignedBigInteger('user_pr_id')->nullable()->default(null);
            $table->string('event', 255);
            $table->string('event_id', 255);
            $table->text('response');
            $table->text('additional_data')->nullable()->default(null);
            $table->unsignedTinyInteger('status')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_pr_id')->references('id')->on('user_pr');
            $table->foreign('candidatecv_id')->references('id')->on('candidatecv');
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
        Schema::dropIfExists('agrello_responses');
    }
}
