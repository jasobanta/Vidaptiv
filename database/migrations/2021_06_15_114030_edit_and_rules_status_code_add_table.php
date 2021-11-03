<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditAndRulesStatusCodeAddTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('edi_titles', function (Blueprint $table) {
            $table->string('status_code')->nullable()->after('title')->index();
        });

        Schema::table('rules_titles', function (Blueprint $table) {
            $table->string('status_code')->nullable()->after('title')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('edi_titles', function($table) {
            $table->dropColumn('status_code');
        });
        Schema::table('rules_titles', function($table) {
            $table->dropColumn('status_code');
        });
    }

}
