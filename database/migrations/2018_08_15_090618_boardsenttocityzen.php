<?php

use App\CityzenBoard;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Boardsenttocityzen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cityzen_boards', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('date_sent');
            $table->unsignedInteger('cityzen_id');
            $table->unsignedInteger('board_id');
            $table->foreign('cityzen_id')->references('id')->on('cityzens');
            $table->foreign('board_id')->references('id')->on('boards');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cityzen_boards');
    }
}
