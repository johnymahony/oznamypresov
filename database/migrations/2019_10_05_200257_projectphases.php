<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Projectphases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('phases', function (Blueprint $table) {
            $table->dropColumn('project_id');
        });

        \App\Phase::create(['name' => 'Nový projekt']);
        \App\Phase::create(['name' => 'Začiatok EIA - posudzovanie vplyvov na zivotné prostredie']);
        \App\Phase::create(['name' => 'Koniec EIA - posudzovanie vplyvov na zivotné prostredie']);
        \App\Phase::create(['name' => 'Územné rozhodovanie']);
        \App\Phase::create(['name' => 'Koniec územneho rozhodovania']);
        \App\Phase::create(['name' => 'Stavebné povoľovanie']);
        \App\Phase::create(['name' => 'Koniec stavebného povoľovania']);
        \App\Phase::create(['name' => 'Kolaudačné konanie']);
        \App\Phase::create(['name' => 'Koniec kolaudačného konania']);

        Schema::table('boards', function (Blueprint $table) {
            $table->unsignedInteger('project_id')->nullable();
            $table->foreign('project_id')->references('id')->on('projects');
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('phase_id')->references('id')->on('phases');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('phases', function (Blueprint $table) {
            //
        });
    }
}
