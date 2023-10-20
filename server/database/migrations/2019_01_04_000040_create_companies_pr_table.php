<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesPrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::dropIfExists('company_pr');
        Schema::create('companies_pr', function (Blueprint $table) {
            $table->id();
            $table->string('invoicing_info', 500);
            $table->unsignedBigInteger('industry_id')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->foreign('industry_id')->references('id')->on('company_industries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies_pr');
    }
}
