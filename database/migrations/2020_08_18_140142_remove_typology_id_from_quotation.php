<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveTypologyIdFromQuotation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotation', function (Blueprint $table) {
            $table->dropForeign('quotation_typology_id_foreign');

            $table->dropColumn('typology_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotation', function (Blueprint $table) {
            $table->biginteger('typology_id')->unsigned()->nullable()->after('invoice_amount');

            $table->foreign('typology_id')->references('id')->on('typology')->onDelete('set null');
        });
    }
}
