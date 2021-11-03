<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableEdiMetaDatas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('edi_meta_datas', function (Blueprint $table) {
            $table->dropColumn('action_code');
            $table->dropColumn('description');
            $table->boolean('is_accepted')->default(0);
            $table->boolean('is_carrier_reject')->default(0);
            $table->boolean('is_bdp_reject')->default(0);
            $table->string('carrier_reject_msg')->nullable();
            $table->string('bdp_reject_msg')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('edi_meta_datas', function (Blueprint $table) {
            $table->dropColumn('is_accepted');
            $table->dropColumn('is_carrier_reject');
            $table->dropColumn('is_bdp_reject');
            $table->dropColumn('carrier_reject_msg');
            $table->dropColumn('bdp_reject_msg');
        });
        //
    }
}
