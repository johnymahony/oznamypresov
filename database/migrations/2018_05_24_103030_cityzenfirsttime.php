<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Cityzenfirsttime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cityzens', function (Blueprint $table) {
            $table->boolean('first_time')->default(true);
        });
        \Illuminate\Support\Facades\DB::table('cityzens')->update(['first_time' => false]);
        \Illuminate\Support\Facades\DB::table('cityzens')->whereNull('radius')->update(['radius' => 500]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cityzens', function (Blueprint $table) {
            $table->dropColumn('first_time');
        });
    }
}
