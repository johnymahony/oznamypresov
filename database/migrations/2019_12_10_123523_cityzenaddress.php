<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Cityzenaddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cityzens', function (Blueprint $table) {
            $table->string('step')->nullable();
            $table->string('street')->nullable();
            $table->string('house_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cityzens', function (Blueprint $table) {
            $table->dropColumn('step');
            $table->dropColumn('street');
            $table->dropColumn('house_number');
        });
    }
}
