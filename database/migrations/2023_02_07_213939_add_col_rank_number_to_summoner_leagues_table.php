<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('summoner_leagues', function (Blueprint $table) {
            $table->integer('rank_number')->after('rank');
        });
    }

    public function down()
    {
        Schema::table('summoner_leagues', function (Blueprint $table) {
            $table->dropColumn('rank_number');
        });
    }
};
