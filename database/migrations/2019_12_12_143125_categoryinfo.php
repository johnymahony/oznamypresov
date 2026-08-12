<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Categoryinfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('info')->nullable();
        });
        \App\Category::where(['name' => 'Distribúcia elektriny - prerušenie'])->update(['info' => 'Oznámenia o prerušení dodávky elektrickej energie.']);
        \App\Category::where(['name' => 'Doručenie / uloženie písomnosti'])->update(['info' => 'Doručenie / uloženie písomnosti.']);
        \App\Category::where(['name' => 'Oznámenia samosprávy'])->update(['info' => 'Oznámenia samosprávy.']);
        \App\Category::where(['name' => 'VZN mesta Prešov'])->update(['info' => 'Všeobecné záväzné nariadenia mesta Prešov.']);
        \App\Category::where(['name' => 'Oznámenia Stavebného úradu'])->update(['info' => 'Stavebné zámery, stavebné povolenia, kolaudačné rozhodnutia, atď...']);
        \App\Category::where(['name' => 'Výberové konania'])->update(['info' => 'Výberové konania na voľné pracovné miesta.']);
        \App\Category::where(['name' => 'Dražby'])->update(['info' => 'Dražby predaja, alebo prenájmu mestkého majetku.']);
        \App\Category::where(['name' => 'Prenájom / predaj nehnuteľností (zámer)'])->update(['info' => 'Predaj, alebo prenájom mestského majetku.']);
        \App\Category::where(['name' => 'Výrub drevín / krovia'])->update(['info' => 'Výrub drevín / krovia.']);
        \App\Category::where(['name' => 'Oznámenia Okresného úradu'])->update(['info' => 'Oznámenia Okresného úradu.']);
        \App\Category::where(['name' => 'Podnety od občanov'])->update(['info' => 'Podnety od občanov.']);
        \App\Category::where(['name' => 'Voľby'])->update(['info' => 'Oznmámenia o volebných miestnostiach, atď...']);
        \App\Category::where(['name' => 'Oznámenia o zrušení trvalého pobytu'])->update(['info' => 'Oznámenia o zrušení trvalého pobytu.']);
        \App\Category::where(['name' => 'Oznámenia o uložení/doručení písomností'])->update(['info' => 'Oznámenia o uložení/doručení písomností.']);
        \App\Category::where(['name' => 'Oznámenia PSK'])->update(['info' => 'Oznmámenia Prešovského samosprávneho kraja.']);
        \App\Category::where(['name' => 'Obchodná verejná súťaž'])->update(['info' => 'Obchodná verejná súťaž']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('info');
        });
    }
}
