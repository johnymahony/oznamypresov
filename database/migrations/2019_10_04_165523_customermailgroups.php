<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Customermailgroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cityzens', function (Blueprint $table) {
            $table->integer('min_emails')->default(2);
            $table->integer('day_emails')->default(3);
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
            $table->dropColumn('min_emails');
            $table->dropColumn('day_emails');
        });
    }
}
