<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->string('email_from')->index();
            $table->text('email_to');
            $table->text('email_cc')->nullable();
            $table->text('email_bcc')->nullable();
            $table->string('subject')->index()->nullable();
            $table->text('message')->nullable(); 
            $table->string('module')->index()->nullable();
            $table->tinyInteger('in_or_out')->index()->default(1);
            $table->integer('user_id')->index();
            $table->mediumText('form_state')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emails');
    }
}
