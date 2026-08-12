<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CadasterNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cadaster_numbers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('city');
        });
        foreach (city()->cadasterNumbers() as $id => $city) {
            if (empty($id)) {
                continue;
            }
            \App\CadasterNumber::create([
                'id' => $id,
                'city' => $city
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cadaster_numbers');
    }
}
