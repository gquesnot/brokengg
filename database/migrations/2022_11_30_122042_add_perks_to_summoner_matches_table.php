<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('summoner_matches', function (Blueprint $table) {
            $table->json('perks');
        });
    }

    public function down()
    {
        Schema::table('summoner_matches', function (Blueprint $table) {
            $table->dropColumn('perks');
        });
    }
};
