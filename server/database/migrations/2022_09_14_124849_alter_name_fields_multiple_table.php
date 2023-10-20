<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNameFieldsMultipleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name', 255)->change();
        });

        Schema::table('job', function (Blueprint $table) {
            $table->string('title', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name', 50)->change();
        });

        Schema::table('job', function (Blueprint $table) {
            $table->string('title', 100)->change();
        });
    }
}
