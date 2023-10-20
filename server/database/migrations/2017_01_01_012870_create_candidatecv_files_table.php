<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidatecvFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidatecv_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidatecv_id');
            $table->string('file_name', 50);
            $table->string('path', 255);
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('candidatecv_files');
    }
}
