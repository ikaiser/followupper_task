<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ManageMultipleCompanyContact extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('quotation_company_contacts', function (Blueprint $table) {
          $table->bigInteger('quotation_id')->unsigned();
          $table->bigInteger('ccontact_id')->unsigned();
          $table->primary(array('quotation_id', 'ccontact_id'));
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_contact_id');
    }
}
