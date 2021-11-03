<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEmailTemplateTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (Schema::hasTable('email_setups')) {
            Schema::table('email_setups', function (Blueprint $table) {
                $table->string('template_title')->after('type_id')->nullable();
                $table->boolean('hide')->after('type_id')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('email_setups', function (Blueprint $table) {
            $table->dropColumn('hide');
            $table->dropColumn('template_title');
        });
    }

}
