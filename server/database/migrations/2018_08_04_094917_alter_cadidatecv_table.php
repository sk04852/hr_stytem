<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCadidatecvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidatecv', function (Blueprint $table) {
            $table->dropColumn([
                'created_by',
                'updated_by'
            ]);
            $table->enum('job_type', ['part_time', 'full_time'])->after('location')->default('full_time');
            $table->unsignedBigInteger('action_id')->default(1)->after('newsletter');
            $table->unsignedBigInteger('user_id')->default(1)->after('action_id');
            $table->foreign('action_id')->references('id')->on('actions');
            $table->foreign('user_id')->references('id')->on('user_pr');
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
        Schema::table('candidatecv', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['action_id']);
            $table->dropColumn([
                'job_type',
                'user_id',
                'action_id'
            ]);
            $table->integer('created_by')->comment('Owner');
            $table->integer('updated_by');
        });
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
