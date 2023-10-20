<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTimelineActionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('timeline_action_types', function (Blueprint $table){
            $table->string('category', 255)->nullable()->default(null)->after('name');
        });

        Schema::table('candidatecv', function(Blueprint $table){
            $table->text('timeline_action_type_comment')->nullable()->default(null)->after('timeline_action_type_id');
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
                'timeline_action_type_comment'
            ]);
        });

        Schema::table('timeline_action_types', function(Blueprint $table){
            $table->dropColumn([
                'category'
            ]);
        });
    }
}
