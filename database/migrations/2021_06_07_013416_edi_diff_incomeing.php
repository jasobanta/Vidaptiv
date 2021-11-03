<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EdiDiffIncomeing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		if (!Schema::hasTable('edi_datas')) {
			// The "edi_table" table exists...
			Schema::create('edi_datas', function (Blueprint $table) {
				$table->id();
				$table->string('owner_email')->nullable();
				$table->timestamp('dtm')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
				$table->string('booking_no')->nullable();
				$table->string('ff_no')->nullable();
				$table->string('bn_no')->nullable();
				$table->string('carrier')->nullable();
				$table->boolean('in_or_out')->default(0);
				$table->string('data')->nullable();
				$table->text('not_in_out')->nullable();
				$table->text('not_in_in')->nullable();
				$table->text('diff')->nullable();
				$table->tinyInteger('status')->default(0);
				$table->tinyInteger('compared_with')->default(0);
				$table->timestamp('compared_at')->default(DB::raw('CURRENT_TIMESTAMP'));
				$table->timestampsTz();
			});			
		}
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('edi_datas');
    }
}
