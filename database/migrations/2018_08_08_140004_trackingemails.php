<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Trackingemails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracked_emails', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('blade');
            $table->unsignedInteger('cityzen_id');
            $table->dateTime('date_sent');
            $table->foreign('cityzen_id')->references('id')->on('cityzens');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracked_emails');
    }
}
