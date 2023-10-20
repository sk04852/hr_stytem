<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMailTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_templates', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->string("template_key")->unique();
            $table->string("to")->nullable()->default(null);
            $table->string("cc")->nullable()->default(null);
            $table->string("subject");
            $table->text("body");
            $table->unsignedBigInteger("created_by");
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->foreign('created_by')->references('id')->on('user_pr');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail_templates');
    }
}
