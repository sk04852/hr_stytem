<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterJobTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_pr', function (Blueprint $table) {
            $table->dropColumn([
                'companypr_ID'
            ]);
            $table->unsignedBigInteger('company_pr_id')->nullable()->after('job_id');
            $table->foreign('company_pr_id')->references('id')->on('companies_pr');
        });

        Schema::table('job', function (Blueprint $table) {
            $table->unsignedBigInteger('job_pr_id')->nullable()->after('job_ID');
            $table->unsignedBigInteger('company_pr_id')->nullable()->after('job_pr_id');
            $table->foreign('job_pr_id')->references('id')->on('job_pr');
            $table->foreign('company_pr_id')->references('id')->on('companies_pr');
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
            $table->dropForeign(['company_pr_id']);
            $table->dropForeign(['job_pr_id']);
            $table->dropColumn([
                'company_pr_id'
            ]);
            $table->dropColumn([
                'job_pr_id'
            ]);
        });

        Schema::table('job_pr', function (Blueprint $table) {
            $table->dropForeign(['company_pr_id']);
            $table->dropColumn([
                'company_pr_id'
            ]);
            $table->integer('companypr_ID');
        });
    }
}
