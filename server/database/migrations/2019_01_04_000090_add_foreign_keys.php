<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_contacts', function (Blueprint $table) {
            $table->unsignedBigInteger('contact_ID')->nullable()->after('id');
            $table->foreign('contact_ID')->references('id')->on('company_contacts');
        });

        Schema::table('company_locations', function (Blueprint $table) {
            $table->unsignedBigInteger('location_ID')->nullable()->after('id');
            $table->foreign('location_ID')->references('id')->on('company_locations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_locations', function (Blueprint $table) {
            $table->dropForeign(['location_ID']);
            $table->dropColumn([
                'location_ID'
            ]);
        });

        Schema::table('company_contacts', function (Blueprint $table) {
            $table->dropForeign(['contact_ID']);
            $table->dropColumn([
                'contact_ID'
            ]);
        });
    }
}
