<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEdidatasForDocumentStatusField extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        //
        if (Schema::hasTable('edi_datas')) {
            Schema::table('edi_datas', function (Blueprint $table) {
                $table->boolean('is_locked')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        if (Schema::hasTable('edi_datas')) {
            Schema::table('edi_datas', function (Blueprint $table) {
                $table->dropColumn('is_locked');
            });
        }
        //
    }

}
