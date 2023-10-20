<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alter10JobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs', function(Blueprint $table){
            $table->text('benefits')->nullable()->default(null)->change();
            $table->text('description')->nullable()->default(null)->change();
            $table->string('location', 255)->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jobs', function(Blueprint $table){
            $table->text('benefits')->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
            $table->string('location', 255)->nullable(false)->change();
        });
    }
}
