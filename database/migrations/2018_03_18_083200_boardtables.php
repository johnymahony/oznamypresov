<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Boardtables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
        });

        Schema::create('boards', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('title');
            $table->string('link');
            $table->text('description');
            $table->dateTime('published_at');
            $table->dateTime('published_uet_until');
            $table->dateTime('note_until');
            $table->unsignedInteger('category_id');
            $table->string('status');
            $table->foreign('category_id')->references('id')->on('categories');

        });
        Schema::create('attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('title');
            $table->string('path');
            $table->unsignedInteger('board_id');
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
        Schema::dropIfExists('attachments');
        Schema::dropIfExists('boards');
        Schema::dropIfExists('categories');
    }
}
