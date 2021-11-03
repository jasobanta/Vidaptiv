<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryCodeInCountryDocumentsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('country_documents', function (Blueprint $table) {
            $table->string('country_code', 6)->after('country_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('country_documents', function (Blueprint $table) {
            $table->dropColumn('country_code');
        });
    }

}
