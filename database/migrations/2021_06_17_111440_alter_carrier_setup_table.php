<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCarrierSetupTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('carrier_setups', function (Blueprint $table) {
            $table->tinyInteger('is_ftp')->after('ftp_password')->default(0);
            $table->string('msg_type', 5)->after('ftp_password')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('carrier_setups', function($table) {
            $table->dropColumn('is_ftp');
            $table->dropColumn('msg_type');
        });
    }

}
