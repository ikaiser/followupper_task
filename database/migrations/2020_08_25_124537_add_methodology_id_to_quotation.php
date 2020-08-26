<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMethodologyIdToQuotation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotation', function (Blueprint $table) {
            $table->bigInteger('methodology_id')->unsigned()->nullable()->after('status_id');

            $table->foreign('methodology_id')->references('id')->on('methodology')->onDelete('set null');
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
            $table->dropColumn('methodology_id');

            $table->dropForeign('quotation_methodology_id_foreign');
        });
    }
}
