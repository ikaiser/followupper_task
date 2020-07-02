<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDcParentConstrain extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('datacuration', function (Blueprint $table) {
            $table->foreign('parent_dc')->references('id')->on('datacuration')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('datacuration', function (Blueprint $table) {
            $table->dropForeign('datacuration_parent_dc_foreign');
        });
    }
}
