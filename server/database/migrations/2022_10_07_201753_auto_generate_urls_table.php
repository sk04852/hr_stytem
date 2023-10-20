<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AutoGenerateUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_generate_urls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('model');
            $table->string('token', 255);
            $table->enum('status', ['valid', 'invalid']);
            $table->unsignedBigInteger('user_pr_id')->comment('The Person who created url');
            $table->timestamps();
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
        Schema::dropIfExists('auto_generate_urls');
    }
}
