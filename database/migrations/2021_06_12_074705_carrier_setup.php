<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CarrierSetup extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('carrier_setups')) {
            // The "email_setup" table exists...
            Schema::create('carrier_setups', function (Blueprint $table) {
                $table->id();
                $table->string('carrier_name')->index();
                $table->string('carrier_email')->index();
                $table->string('carrier_scac')->index()->nullable();
                $table->string('ftp_location')->index()->nullable();
                $table->string('bdp_owner')->index()->nullable();
                $table->string('ftp_userid')->index()->nullable();
                $table->text('ftp_password')->nullable();
                $table->tinyInteger('reply_via_email')->default(0);
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
        Schema::dropIfExists('carrier_setups');
    }

}
