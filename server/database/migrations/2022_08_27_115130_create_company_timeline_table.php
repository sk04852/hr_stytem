<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyTimelineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_timeline', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_pr_id');
            $table->unsignedBigInteger('candidatecv_id')->nullable()->default(null);
            $table->unsignedBigInteger('user_pr_id')->nullable()->default(null);
            $table->string('action_name', 50);
            $table->string('comments', 100)->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->foreign('company_pr_id')->references('id')->on('companies_pr');
            $table->foreign('candidatecv_id')->references('id')->on('candidatecv');
            $table->foreign('user_pr_id')->references('id')->on('user_pr');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_timeline');
    }
}
