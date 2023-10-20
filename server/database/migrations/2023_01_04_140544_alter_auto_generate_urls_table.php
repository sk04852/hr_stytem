<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAutoGenerateUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auto_generate_urls', function(Blueprint $table){
            $table->unsignedBigInteger('company_contact_id')->nullable()->default(null)->after('user_pr_id');

            $table->foreign('company_contact_id')->references('id')->on('company_contacts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auto_generate_urls', function(Blueprint $table){
            $table->dropForeign([
                'company_contact_id'
            ]);

            $table->dropColumn([
                'company_contact_id'
            ]);
        });
    }
}
