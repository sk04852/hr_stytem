<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Alter1CompanyPrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies_pr', function (Blueprint $table){
            $table->dropForeign([
                'industry_id'
            ]);

            $table->dropColumn([
                'industry_id'
            ]);
        });

        Schema::create('companies_industries', function(Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('company_pr_id')->nullable()->default(null);
            $table->unsignedBigInteger('industry_id')->nullable()->default(null);
            $table->foreign('company_pr_id')->references('id')->on('companies_pr');
            $table->foreign('industry_id')->references('id')->on('company_industries');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies_industries');

        Schema::table('companies_pr', function (Blueprint $table){
            $table->unsignedBigInteger('industry_id')->nullable()->default(null);
            $table->foreign('industry_id')->references('id')->on('company_industries');
        });
    }
}
