<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableEdiDatasCountryId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (Schema::hasTable('edi_datas')) {
            Schema::table('edi_datas', function (Blueprint $table) {
                $table->integer('country_id')->after('owner_name')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        if (Schema::hasTable('edi_datas')) {
            Schema::table('edi_datas', function (Blueprint $table) {
                $table->dropColumn('country_id');
            });
        }
    }
}
