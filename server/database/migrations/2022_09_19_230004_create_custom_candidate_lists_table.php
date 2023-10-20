<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomCandidateListsTable extends Migration
{
    
    public function up()
    {
        Schema::create('custom_candidate_lists', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->enum('gender', ['male', 'female']);
            $table->date('dob');
            $table->string('phone', 20);
            $table->string('email', 255);
            $table->string('location', 255);
            $table->unsignedBigInteger('mother_language_id');
            $table->text('description');
            $table->string('source', 100);
            $table->string('photo', 255);
            $table->boolean('consent')->default(0);
            $table->boolean('newsletter')->default(0);
            $table->integer('created_by')->comment('Owner');
            $table->integer('updated_by');
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->foreign('mother_language_id')->references('id')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_candidate_lists');
    }
}
