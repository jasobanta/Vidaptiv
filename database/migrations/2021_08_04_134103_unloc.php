<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Unloc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unlocs', function (Blueprint $table) {
            $table->id();
            $table->string('lo_code')->nullable();
            $table->string('country')->nullable();
            $table->string('code')->nullable();
            $table->string('country_name')->nullable();
            $table->mediumText('rules')->nullable();
            $table->softDeletes();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unlocs');
    }
}
