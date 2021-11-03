<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCarrierSetupsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('carrier_setups', function (Blueprint $table) {
            $table->string('msg_type', 5)->default("IN")->change();
            $table->string('folder_location')->after('msg_type')->nullable();
            $table->renameColumn('msg_type', 'folder_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('carrier_setups', function (Blueprint $table) {
            $table->renameColumn('folder_type', 'msg_type');
            $table->dropColumn('folder_location');
        });
    }

}
