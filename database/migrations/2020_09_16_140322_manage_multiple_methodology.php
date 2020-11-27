<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ManageMultipleMethodology extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('quotation_methodology', function (Blueprint $table) {
          $table->bigInteger('quotation_id')->unsigned();
          $table->bigInteger('methodology_id')->unsigned();
          $table->primary(array('quotation_id', 'methodology_id'));
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('methodology');
    }
}
