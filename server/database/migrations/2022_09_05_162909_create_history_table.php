<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_history', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference_table');
            $table->integer('reference_id')->unsigned();
            $table->integer('actor_id')->unsigned();
            $table->string('body');
            $table->string('comments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidate_history');
    }
}
