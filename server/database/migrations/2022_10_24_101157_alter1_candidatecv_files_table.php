<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alter1CandidatecvFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidatecv_files', function (Blueprint $table) {
            $table->unsignedBigInteger('user_pr_id')->after('path')->nullable()->comment('The User who uploaded file');
            $table->foreign('user_pr_id')->references('id')->on('user_pr');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidatecv_files', function (Blueprint $table) {
            $table->dropForeign([
                'user_pr_id'
            ]);

            $table->dropColumn([
                'user_pr_id'
            ]);
        });
    }
}
