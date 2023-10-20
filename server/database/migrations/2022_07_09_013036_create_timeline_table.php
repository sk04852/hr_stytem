<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimelineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timeline', function (Blueprint $table) {
            $table->id();
            $table->integer('action_ID');
            $table->integer('candidate_ID');
            $table->integer('userpr_ID');
            $table->string('action_name', 50);
            $table->date('date_added');
            $table->string('comments', 100);
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
        Schema::dropIfExists('timeline');
    }
}
