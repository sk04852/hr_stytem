<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CandidatecvAgreement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agreement_statuses', function (Blueprint $table){
            $table->id();
            $table->string('status', '50');
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });

        Schema::create('candidatecv_agreements', function (Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('candidatecv_id');
            $table->unsignedBigInteger('user_pr_id')->comment('The Person who sign the agreement');
            $table->unsignedBigInteger('agreement_status_id');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('candidatecv_id')->references('id')->on('candidatecv');
            $table->foreign('user_pr_id')->references('id')->on('user_pr');
            $table->foreign('agreement_status_id')->references('id')->on('agreement_statuses');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });
        Schema::create('candidatecv_agreement_files', function (Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('candidatecv_agreement_id');
            $table->string('file_name', 50);
            $table->string('path', 255);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('candidatecv_agreement_id')->references('id')->on('candidatecv_agreements');
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
        Schema::dropIfExists('candidatecv_agreement_files');
        Schema::dropIfExists('candidatecv_agreements');
        Schema::dropIfExists('agreement_statuses');
    }
}
