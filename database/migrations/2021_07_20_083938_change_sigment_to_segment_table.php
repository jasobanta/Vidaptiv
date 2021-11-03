<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSigmentToSegmentTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::rename('rule_sigment_meta_datas', 'rule_segment_meta_datas');
        
        Schema::table('rule_segment_meta_datas', function (Blueprint $table) {
            $table->renameColumn('sigment_field_id', 'segment_field_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::rename('rule_segment_meta_datas', 'rule_sigment_meta_datas');

        Schema::table('rule_sigment_meta_datas', function (Blueprint $table) {
            $table->renameColumn('segment_field_id', 'sigment_field_id');
        });
    }

}
