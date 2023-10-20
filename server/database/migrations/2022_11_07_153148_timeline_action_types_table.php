<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TimelineActionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timeline_action_types', function (Blueprint $table){
            $table->id();
            $table->string('name', 15);
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });

        Schema::table('candidatecv', function(Blueprint $table){
            $table->unsignedBigInteger('timeline_action_type_id')->nullable()->default(null)->after('status');
            $table->foreign('timeline_action_type_id')->references('id')->on('timeline_action_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidatecv', function(Blueprint $table){
            $table->dropColumn([
                'timeline_action_type_id'
            ]);
        });
        Schema::dropIfExists('timeline_action_types');
    }
}
