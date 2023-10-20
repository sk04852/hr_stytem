<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidatecvTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidatecv_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('candidatecv_id');
            $table->unsignedBigInteger('tag_id');
            $table->foreign('candidatecv_id')->references('id')->on('candidatecv');
            $table->foreign('tag_id')->references('id')->on('tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidatecv_tag');
    }
}
