<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IdIncrement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('timeline', function (Blueprint $table) {
            DB::statement('ALTER TABLE timeline MODIFY COLUMN id INT NOT NULL UNIQUE AUTO_INCREMENT FIRST');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('timeline', function (Blueprint $table) {
            //
        });
    }
}
