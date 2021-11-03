<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCountries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn('destination_country_code');
            $table->dropColumn('destination_country_name');
            $table->dropColumn('country_bl_requirements');
            $table->string('country_code')->after('id');
            $table->string('country_name')->after('country_code');
            $table->tinyInteger('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn('country_code');
            $table->dropColumn('country_name');
            $table->dropColumn('status');
        });
    }
}
