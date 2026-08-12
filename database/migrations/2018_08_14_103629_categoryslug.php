<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Categoryslug extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('slug')->nullable();
        });
        foreach (\App\Category::all() as $category) {
            $category->update(['slug' => $category->toSlug()]);
            dispatchJob(new \App\Jobs\AddToSitemap(url('/').'/board?rok='.date('Y').'&kategoria='.$category->id.'-'.$category->slug.''));
            dispatchJob(new \App\Jobs\AddToSitemap(url('/').'/mapa?rok='.date('Y').'&kategoria='.$category->id.'-'.$category->slug.''));
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
}
