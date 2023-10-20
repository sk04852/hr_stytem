<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alter3JobTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job', function (Blueprint $table) {
            $table->string('contactName', 255);
            $table->string('type_Of_Job_Comment', 500)->nullable()->default(null)->change();
            $table->string('transportComment', 500)->nullable()->default(null)->change();
            $table->string('workingHoursComment', 500)->nullable()->default(null)->change();
            $table->string('clothesComment', 500)->nullable()->default(null)->change();
            $table->integer('salary2')->nullable()->default(null)->change();

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
            $table->integer('salary2')->nullable(false)->change();
            $table->string('clothesComment', 45)->nullable(false)->change();
            $table->string('workingHoursComment', 45)->nullable(false)->change();
            $table->string('transportComment', 45)->nullable(false)->change();
            $table->string('type_Of_Job_Comment', 45)->nullable(false)->change();
            $table->dropColumn([
                'contactName'
            ]);
        });
    }
}
