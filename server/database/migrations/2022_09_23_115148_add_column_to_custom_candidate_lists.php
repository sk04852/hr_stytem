<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToCustomCandidateLists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_candidate_lists', function (Blueprint $table) {
            $table->string('uuid')->nullable();
            $table->string('job_type')->nullable();
            $table->tinyInteger('action_id')->default(0);
            $table->tinyInteger('user_id');
            $table->date('dob')->nullable()->change();
            $table->string('phone', 20)->nullable()->change();
            $table->string('email', 255)->nullable()->change();
            $table->string('location', 255)->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->string('source', 100)->nullable()->change();
            $table->string('photo', 255)->nullable()->change();
            $table->dropForeign('custom_candidate_lists_mother_language_id_foreign');
            $table->dropColumn('mother_language_id');
            $table->string('mother_language')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custom_candidate_lists', function (Blueprint $table) {
            $table->dropColumn('uuid');
            $table->dropColumn('job_type');
            $table->dropColumn('action_id');
            $table->dropColumn('user_id');
            $table->date('dob')->change();
            $table->string('phone', 20)->change();
            $table->string('email', 255)->change();
            $table->string('location', 255)->change();
            $table->text('description')->change();
            $table->string('source', 100)->change();
            $table->string('photo', 255)->change();
            $table->dropColumn('mother_language');
            $table->unsignedBigInteger('mother_language_id');
            $table->foreign('mother_language_id')->references('id')->on('languages');
        });
    }
}
