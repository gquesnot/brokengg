<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('summoner_leagues', function (Blueprint $table) {
            $table->id();
            $table->enum('type', \App\Enums\RankedType::values());
            $table->foreignIdFor(\App\Models\Summoner::class, 'summoner_id')->constrained();
            $table->enum('rank', \App\Enums\Rank::values());
            $table->enum('tier', \App\Enums\Tier::values());
        });
    }

    public function down()
    {
        Schema::dropIfExists('summoner_leagues');
    }
};
