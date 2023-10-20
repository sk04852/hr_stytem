<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alter3CandidateCVTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mail_templates', function (Blueprint $table) {
            $table->string("subject")->nullable()->default(null)->change();
        });

        Schema::table('candidatecv_language', function (Blueprint $table) {
            $table->string("level",50)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidatecv_language', function (Blueprint $table) {
            $table->enum('level', ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'])->change();
        });

        Schema::table('mail_templates', function (Blueprint $table) {
            $table->string("subject")->nullable(false)->change();
        });
    }
}
