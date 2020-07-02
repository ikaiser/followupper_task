<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSendMailInDatacurationElement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('datacuration_element', function (Blueprint $table) {
            $table->tinyInteger('send_email')->default(1)->after('extension');
            $table->dropColumn('tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('datacuration_element', function (Blueprint $table) {
            $table->dropColumn('send_email');
            $table->string('tags')->after('topic')->nullable();
        });
    }
}
