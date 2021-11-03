<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CarrierRulesIgnore extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('carrier_rule_meta_datas')) {
            Schema::create('carrier_rule_meta_datas', function (Blueprint $table) {
                $table->id();
                $table->integer('carrier_id')->index();
                $table->integer('rule_id')->index();
                $table->tinyInteger('status')->index()->default(1);
                $table->softDeletes();
                $table->timestampsTz();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        if (!Schema::hasTable('carrier_rule_meta_datas')) {
            Schema::dropIfExists('carrier_rule_meta_datas');
        }
    }

}
