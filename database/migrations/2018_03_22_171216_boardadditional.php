<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Boardadditional extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->boolean('sent_daily')->default(false);
            $table->boolean('sent_weekly')->default(false);
            $table->dateTime('ready_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->dropColumn('sent_daily');
            $table->dropColumn('sent_weekly');
            $table->dropColumn('ready_at');
        });
    }
}
