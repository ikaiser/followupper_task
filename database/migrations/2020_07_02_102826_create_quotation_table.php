<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('user_id')->unsigned();
            $table->bigInteger('company_id')->unsigned();
            $table->bigInteger('company_contact_id')->unsigned()->nullable();
            $table->integer('sequential_number');
            $table->string('code');
            $table->text('description')->nullable();
            $table->date('insertion_date');
            $table->date('deadline');
            $table->integer('amount')->nullable();
            $table->bigInteger('status_id')->unsigned()->nullable();
            $table->integer('amount_acquired')->nullable();
            $table->integer('chance')->unsigned();
            $table->text('feedback');
            $table->smallInteger('closed');
            $table->integer('invoice_amount')->unsigned()->nullable();
            $table->biginteger('typology_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('company')->onDelete('cascade');
            $table->foreign('company_contact_id')->references('id')->on('company_contact')->onDelete('set null');
            $table->foreign('status_id')->references('id')->on('status')->onDelete('set null');
            $table->foreign('typology_id')->references('id')->on('typology')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotation');
    }
}
