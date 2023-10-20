<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_pr', function (Blueprint $table) {
            $table->id();
            $table->integer('userpr_ID');
            $table->integer('user_ID')->index('user_pr_ibfk_1');
            $table->binary('photo');
            $table->string('int_code', 5);
            $table->string('phone', 20);
            $table->string('email', 50);
            $table->string('password', 50);
            $table->string('timezone', 10);
            $table->integer('permission_ID');
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
        Schema::dropIfExists('user_pr');
    }
}
