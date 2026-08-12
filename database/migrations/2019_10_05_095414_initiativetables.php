<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Initiativetables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->string('iniciative_type')->nullable();
            $table->string('address')->nullable();
            $table->text('solution')->nullable();
            $table->string('person')->nullable();
            $table->string('solution_state')->nullable();
            $table->date('solution_date')->nullable();
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
            $table->dropColumn('address');
            $table->dropColumn('iniciative_type');
            $table->dropColumn('solution');
            $table->dropColumn('person');
            $table->dropColumn('solution_state');
        });
    }
}
