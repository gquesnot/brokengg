<?php

use App\Models\Summoner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('summoner_apis', function (Blueprint $table) {
            $table->id();
            $table->string('api_summoner_id')->nullable();
            $table->string('api_account_id')->nullable();
            $table->string('puuid')->nullable();
            $table->foreignIdFor(Summoner::class)->constrained();
            $table->foreignIdFor(\App\Models\ApiAccount::class, 'account_id')->constrained();
        });
    }

    public function down()
    {
        Schema::dropIfExists('summoner_api_keys');
    }
};