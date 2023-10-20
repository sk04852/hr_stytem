<?php

use App\Http\Controllers\Jobs\Models\JobPr;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alter11JobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs_pr', function(Blueprint $table){
            $table->unsignedTinyInteger('duration_type')->default(JobPr::JOB_DURATION_PERMANENT)->after('job_type');
            $table->unsignedTinyInteger('employment_type')->default(JobPr::EMPLOYMENT_TYPE_FULL_TIME)->after('duration_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jobs_pr', function(Blueprint $table){
            $table->dropColumn([
                'duration_type',
                'employment_type'
            ]);
        });
    }
}
