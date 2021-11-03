<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTemplateTypeInEmailSetupTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('email_setups', function (Blueprint $table) {
            $table->tinyInteger('type_id')->after('id')->index()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('email_setups', function (Blueprint $table) {
            $table->dropColumn('type_id');
        });
    }

}
