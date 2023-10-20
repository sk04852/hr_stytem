<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HrTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_task_statuses', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });

        Schema::create('hr_tasks', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->date('deadline');
            $table->unsignedBigInteger("hr_task_status_id");
            $table->unsignedBigInteger("created_by");
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->foreign('hr_task_status_id')->references('id')->on('hr_task_statuses');
            $table->foreign('created_by')->references('id')->on('user_pr');
        });

        Schema::create('hr_assigned_tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('user_pr_id');
            $table->unsignedBigInteger('hr_task_id');
            $table->foreign('user_pr_id')->references('id')->on('user_pr');
            $table->foreign('hr_task_id')->references('id')->on('hr_tasks');
            $table->timestamps();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_assigned_tasks');
        Schema::dropIfExists('hr_tasks');
        Schema::dropIfExists('hr_task_statuses');
    }
}
