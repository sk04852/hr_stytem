<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobUpdateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('updatejobs', function (Blueprint $table) {
            $table->id();
            $table->integer('job_ID');
            $table->string('title', 100);
            $table->boolean('status');
            $table->char('type_of_job', 15);
            $table->integer('category_ID');
            $table->integer('update_ID');
            $table->string('company', 30);
            $table->string('location', 200);
            $table->string('contact', 500);
            $table->integer('doc_ID');
            $table->string('doc', 500);
            $table->string('jobDescription', 500);
            $table->string('divDescription', 500);
            $table->string('comments', 500);
            $table->string('benefits', 500);
            $table->integer('tag_ID');
            $table->string('required', 500);
            $table->string('creator', 500);
//            $table->timestamps('created_at');
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
        Schema::dropIfExists('updatejobs');
    }
}
