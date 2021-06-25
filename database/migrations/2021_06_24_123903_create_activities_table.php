<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text("name");
            $table->text("description")->nullable();
            $table->timestamps();
        });

        Schema::create('todo_activities', function (Blueprint $table) {
            $table->bigInteger('todo_id')->unsigned();
            $table->bigInteger('activity_id')->unsigned();
            $table->primary(array('todo_id', 'activity_id'));

            $table->foreign('todo_id')->references('id')->on('todos')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activities');
        Schema::dropIfExists('todo_activities');
    }
}
