<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alter10CandidatecvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidatecv', function (Blueprint $table) {
            $table->dropForeign([
                'action_id'
            ]);
            $table->dropColumn([
                'action_id'
            ]);
        });

        Schema::table('actions', function (Blueprint $table) {
            $table->unsignedInteger('counts')->default(0)->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('actions', function (Blueprint $table) {
            $table->dropColumn(['counts']);
        });

        Schema::table('candidatecv', function (Blueprint $table) {
            $table->unsignedBigInteger('action_id')->nullable()->default(1)->after('newsletter');
            $table->foreign('action_id')->references('id')->on('actions');
        });
    }
}
