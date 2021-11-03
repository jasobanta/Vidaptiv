<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusRulesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('rules', function (Blueprint $table) {
            $table->tinyInteger('status')->after('priority')->default(1);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('rules', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('deleted_at');
        });
    }

}
