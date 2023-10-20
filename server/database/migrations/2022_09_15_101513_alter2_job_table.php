<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alter2JobTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job', function (Blueprint $table) {
            $table->date('deadline')->change();
            $table->string('creator', 255)->change();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job', function (Blueprint $table) {
            $table->timestamp('deadline')->change();
            $table->integer('creator')->unsigned()->change();
        });
    }
}
