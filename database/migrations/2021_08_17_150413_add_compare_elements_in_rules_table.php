<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompareElementsInRulesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('rules', function (Blueprint $table) {
            $table->string('default_compare_elements')->after('status')->nullable();
            $table->tinyInteger('is_free_text_compare')->after('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('rules', function (Blueprint $table) {
            $table->dropColumn('default_compare_elements');
            $table->dropColumn('is_free_text_compare');
        });
    }

}
