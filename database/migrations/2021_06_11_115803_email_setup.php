<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EmailSetup extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('email_setups')) {
            // The "email_setup" table exists...
            Schema::create('email_setups', function (Blueprint $table) {
                $table->id();
                $table->string('email_to')->index()->nullable();
                $table->string('email_cc')->index()->nullable();
                $table->string('email_bcc')->index()->nullable();
                $table->string('subject')->index()->nullable();
                $table->text('message')->nullable(); 
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
        Schema::dropIfExists('email_setups');
    }

}
