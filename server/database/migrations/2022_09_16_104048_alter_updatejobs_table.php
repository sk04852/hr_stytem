<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUpdatejobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('updatejobs', function (Blueprint $table) {
            $table->string('comments', 500)->nullable()->default(null)->change();
            $table->integer('tag_id')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('updatejobs', function (Blueprint $table) {
            $table->string('comments', 500)->nullable(false)->change();
            $table->integer('tag_id')->nullable(false)->change();
        });
    }
}
