<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RulesTitlesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable('rules_titles')) {
            // The "email_setup" table exists...
            Schema::create('rules_titles', function (Blueprint $table) {
                $table->id();
                $table->string('title')->index();
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
        Schema::dropIfExists('rules_titles');
    }

}
