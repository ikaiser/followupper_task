<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotationHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('quotation_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('field');
            $table->text('old_value');
            $table->text('new_value');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('quotation_id')->references('id')->on('quotation')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotation_history');
    }
}
