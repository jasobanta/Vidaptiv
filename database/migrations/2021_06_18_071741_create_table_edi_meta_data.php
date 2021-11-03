<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableEdiMetaData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('edi_meta_datas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('edi_data_id');
            $table->tinyInteger('action_code')->default(1); // 
            $table->integer('rules_id');
            $table->tinyInteger('reason_code');
            $table->string('description');
            $table->integer('user_id');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_edi_meta_data');
    }
}
