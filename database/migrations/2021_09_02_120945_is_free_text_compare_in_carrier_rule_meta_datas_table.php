<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IsFreeTextCompareInCarrierRuleMetaDatasTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('carrier_rule_meta_datas', function (Blueprint $table) {
            $table->tinyInteger('is_free_text_compare')->after('is_ignore')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('carrier_rule_meta_datas', function (Blueprint $table) {
            $table->dropColumn('is_free_text_compare');
        });
    }

}
