<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCompanyPrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies_pr', function (Blueprint $table) {
            $table->string('address')->nullable()->default(null)->after('invoicing_info');
            $table->string('vat')->nullable()->default(null)->after('address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies_pr', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'vat'
            ]);
        });
    }
}
