<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EdiTableAlter extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('edi_datas', function (Blueprint $table) {
            $table->string('owner_name', 50)->after("owner_email")->nullable();
            $table->integer('diff_seconds')->after('dtm')->default(0);
            $table->timestamp('received_date')->after('dtm')->nullable();
            $table->tinyInteger('sent_emails_count')->default(0);
            $table->softDeletesTz()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('edi_datas', function (Blueprint $table) {
            $table->dropColumn('owner_name');
            $table->dropColumn('received_date');
            $table->dropColumn('diff_seconds');
            $table->dropColumn('sent_emails_count');
            $table->dropColumn('deleted_at');
        });
    }

}
