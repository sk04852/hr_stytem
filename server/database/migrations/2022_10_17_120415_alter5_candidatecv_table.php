<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alter5CandidatecvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type', 255);
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });

        Schema::table('candidatecv', function (Blueprint $table) {
            $table->dropColumn([
                'job_type'
            ]);
        });

        Schema::create('candidate_job_types', function (Blueprint $table) {
            $table->unsignedBigInteger('candidatecv_id');
            $table->unsignedBigInteger('job_type_id');
            $table->foreign('candidatecv_id')->references('id')->on('candidatecv');
            $table->foreign('job_type_id')->references('id')->on('job_types');
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

        Schema::dropIfExists('candidate_job_types');
        Schema::table('candidatecv', function (Blueprint $table) {
            $table->enum('job_type', ['part_time', 'full_time'])->after('mother_language')->default('full_time');
        });
        Schema::dropIfExists('job_types');
    }
}
