<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEmailSetupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_setups', function (Blueprint $table) {
            $table->string('template_types')->after('hide')->default(0);
            $table->tinyInteger('edi_title_id')->after('hide')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_setups', function (Blueprint $table) {
            $table->dropColumn('template_types');
            $table->dropColumn('edi_title_id');
        });
    }
}
