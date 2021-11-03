<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CountryDocumentsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('country_documents', function (Blueprint $table) {
            $table->id();
            $table->string('country_id')->index()->nullable();
            $table->string('country_name')->index()->nullable();
            $table->longText('documents')->nullable();
            $table->tinyInteger('status')->index()->default(1);
            $table->softDeletes();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('country_documents');
    }

}
