<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RuleSigmentMetaData extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('rule_sigment_meta_datas')) {
            Schema::create('rule_sigment_meta_datas', function (Blueprint $table) {
                $table->id();
                $table->integer('rule_id')->index();
                $table->integer('sigment_field_id')->index();
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
        if (!Schema::hasTable('rule_sigment_meta_datas')) {
            Schema::dropIfExists('rule_sigment_meta_datas');
        }
    }

}
