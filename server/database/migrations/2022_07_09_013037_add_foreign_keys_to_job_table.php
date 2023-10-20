<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToJobTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job', function (Blueprint $table) {
            $table->foreignId('tag_ID')->nullable()->references('id')->on('job_tag');
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
            $table->dropForeign('tag_ID');
        });
    }
}
