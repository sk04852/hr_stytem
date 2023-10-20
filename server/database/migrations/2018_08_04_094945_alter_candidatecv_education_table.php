<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCandidatecvEducationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidatecv_education', function (Blueprint $table) {
            $table->dropColumn([
                'degree_name'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidatecv_education', function (Blueprint $table) {
            $table->string('degree_name', 100)->after('candidatecv_id');
        });
    }
}
