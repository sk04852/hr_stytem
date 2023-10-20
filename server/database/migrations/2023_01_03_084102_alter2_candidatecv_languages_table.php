<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alter2CandidatecvLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidatecv_language', function(Blueprint $table){
            $table->string('speaking', 255)->nullable()->default(null)->after('level');
            $table->string('reading_writing', 255)->nullable()->default(null)->after('speaking');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidatecv_language', function(Blueprint $table){
            $table->dropColumn([
                'reading_writing',
                'speaking'
            ]);
        });
    }
}
