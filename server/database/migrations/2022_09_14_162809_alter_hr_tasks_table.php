<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterHrTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hr_tasks', function (Blueprint $table) {
            $table->boolean('notify_incomplete_status')->default(0)->after('created_by');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hr_tasks', function (Blueprint $table) {
            $table->dropColumn([
                'notify_incomplete_status'
            ]);
        });
    }
}
