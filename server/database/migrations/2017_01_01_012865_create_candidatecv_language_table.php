<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidatecvLanguageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidatecv_language', function (Blueprint $table) {
            $table->unsignedBigInteger('candidatecv_id');
            $table->unsignedBigInteger('language_id');
            $table->enum('level', ['A1', 'A2', 'B1', 'B2', 'C1', 'C2']);
            $table->timestamps();
            $table->foreign('candidatecv_id')->references('id')->on('candidatecv');
            $table->foreign('language_id')->references('id')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidatecv_language');
    }
}
