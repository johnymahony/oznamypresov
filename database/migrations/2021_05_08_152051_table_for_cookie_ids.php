<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableForCookieIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cookie_ids', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('ip');
            $table->text('browser');
            $table->unsignedInteger('cityzen_id')->nullable();
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
        Schema::dropIfExists('cookie_ids');
    }
}
