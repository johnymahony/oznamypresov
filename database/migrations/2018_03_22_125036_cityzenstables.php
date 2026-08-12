<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Cityzenstables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cityzens', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('email')->unique()->nullable();
            $table->string('gsm')->unique()->nullable();
            $table->string('hash')->nullable();
            $table->boolean('deleted')->default(false);
            $table->dateTime('hash_until')->nullable();
            $table->string('receive_type')->default('daily');
        });

        Schema::create('cityzen_positions', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('latitude');
            $table->string('longitude');
            $table->integer('radius');
            $table->unsignedInteger('cityzen_id');
            $table->foreign('cityzen_id')->references('id')->on('cityzens')->onDelete('cascade');
        });

        Schema::create('cityzen_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->unsignedInteger('cityzen_id');
            $table->unsignedInteger('category_id');
            $table->foreign('cityzen_id')->references('id')->on('cityzens')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->unsignedInteger('cityzen_id')->nullable();
            $table->integer('job_id')->nullable()->index();
            $table->string('subject')->default('');
            $table->text('text');
            $table->string('recepient');
            $table->string('from');
            $table->string('from_name');
            $table->enum('status', ['?', 'Q', 'S', 'E'])->default('?');
            $table->enum('type', ['email', 'manual_sms', 'auto_sms', 'received_sms', 'letter'])->default('email');
            $table->datetime('sent_date')->nullable();
            $table->foreign('cityzen_id')->references('id')->on('cityzens')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('cityzen_categories');
        Schema::dropIfExists('cityzen_positions');
        Schema::dropIfExists('cityzens');
    }
}
