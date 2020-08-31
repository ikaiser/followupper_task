<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotationTypologyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_typology', function (Blueprint $table) {
            $table->bigInteger('quotation_id')->unsigned();
            $table->bigInteger('typology_id')->unsigned();

            $table->primary(array('quotation_id', 'typology_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotation_typology');
    }
}
