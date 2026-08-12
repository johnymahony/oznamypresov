<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Streetstable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('streets', function (Blueprint $table) {
//            I_GM_ADR - id adresného bodu
//I_CM - id mestskej časti
//N_CM - názov mestskej časti
//I_U - id ulice
//N_U - názov ulice
//CS - súpisné číslo
//CO - orientačné číslo
            $table->increments('id');
            $table->string('name');
            $table->string('cs')->nullable();
            $table->string('co')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->integer('point_id')->nullable();
            $table->integer('city_part_id')->nullable();
            $table->string('city_part_name')->nullable();
            $table->integer('street_id')->nullable();
            $table->string('x')->nullable();
            $table->string('y')->nullable();
        });
        Schema::table('cityzens', function (Blueprint $table) {
            $table->unsignedInteger('street_id')->nullable();
            $table->foreign('street_id')->references('id')->on('streets')->onDelete('cascade');
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
            $table->dropForeign('cityzens_street_id_foreign');
            $table->dropColumn('street_id');
        });
        Schema::dropIfExists('streets');
    }
}
