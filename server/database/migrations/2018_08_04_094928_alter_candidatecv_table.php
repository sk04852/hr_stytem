<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCandidatecvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidatecv', function (Blueprint $table) {
            $table->dropForeign(['mother_language_id']);
            $table->dropColumn([
                'mother_language_id'
            ]);
            $table->string('mother_language', 50)->nullable()->after('location');

            $table->date('dob')->nullable()->default(null)->change();
            $table->string('phone')->nullable()->default(null)->change();
            $table->string('email')->nullable()->default(null)->change();
            $table->string('location')->nullable()->default(null)->change();
            $table->text('description')->nullable()->default(null)->change();
            $table->string('source', 100)->nullable()->default(null)->change();
            $table->string('photo', 255)->nullable()->change();
            $table->boolean('consent')->default(0)->nullable()->change();
            $table->boolean('newsletter')->default(0)->nullable()->change();
            $table->unsignedBigInteger('action_id')->nullable()->default(1)->change();
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        Schema::table('candidatecv_job_history', function (Blueprint $table) {
            $table->date('ending_date')->nullable()->default(null)->change();
            $table->boolean('still_working')->default(0);
        });

        Schema::table('candidatecv_education', function (Blueprint $table) {
            $table->date('ending_date')->nullable()->default(null)->change();
            $table->unsignedBigInteger('degree_id')->default(1)->after('id');
            $table->foreign('degree_id')->references('id')->on('education_degrees');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        Schema::table('candidatecv_education', function (Blueprint $table) {
            $table->dropForeign(['degree_id']);
            $table->dropColumn([
                'degree_id'
            ]);
            $table->date('ending_date')->nullable(false)->change();
        });

        Schema::table('candidatecv_job_history', function (Blueprint $table) {
            $table->dropColumn([
                'still_working'
            ]);
            $table->date('ending_date')->nullable(false)->change();
        });


        Schema::table('candidatecv', function (Blueprint $table) {
            $table->date('dob')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('location')->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
            $table->string('source', 100)->nullable(false)->change();
            $table->string('photo', 255)->nullable(false)->change();
            $table->boolean('consent')->default(0)->nullable(false)->change();
            $table->boolean('newsletter')->default(0)->nullable(false)->change();
            $table->unsignedBigInteger('action_id')->nullable(false)->default(1)->change();
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->dropColumn([
                'mother_language'
            ]);
            $table->unsignedBigInteger('mother_language_id');
            $table->foreign('mother_language_id')->references('id')->on('languages');
        });
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
