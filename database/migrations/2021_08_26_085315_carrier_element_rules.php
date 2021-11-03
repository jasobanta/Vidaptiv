<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CarrierElementRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carrier_rule_meta_datas', function (Blueprint $table) {
            $table->tinyInteger('is_ignore')->after('rule_id')->default(0);
            $table->string('compare_elements',255)->after('rule_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carrier_rule_meta_datas', function (Blueprint $table) {
            $table->dropColumn('is_ignore');
            $table->dropColumn('compare_elements');
        });
    }
}