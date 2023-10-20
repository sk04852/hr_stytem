<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterToCustomCandidateListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_candidate_lists', function (Blueprint $table) {
            $table->BigInteger('list_name_id')->nullable();
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
            $table->dropColumn('list_name_id')->nullable();
        });
    }
}
