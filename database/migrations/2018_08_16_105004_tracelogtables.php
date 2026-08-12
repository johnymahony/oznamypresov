<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Tracelogtables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trace_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user');
            $table->string('model')->index();
            $table->unsignedInteger('model_id')->index();
            $table->dateTime('date');
            $table->string('context_model_name')->nullable();
            $table->string('context_model_id')->nullable();
            $table->string('context_comment')->nullable();

        });
        Schema::create('trace_log_values', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('trace_log_id')->index();
            $table->string('column');
            $table->string('from')->default('')->nullable();
            $table->string('to')->default('')->nullable();
            $table->dateTime('date');
            $table->foreign('trace_log_id')->references('id')->on('trace_log')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trace_log_values');
        Schema::dropIfExists('trace_log');
    }
}
