<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('summoner_matches', function (Blueprint $table) {
            $table->id();
            $table->boolean('won')->default(false);
            $table->float('kill_participation');
            $table->float('kda');
            $table->integer('assists');
            $table->integer('deaths');
            $table->integer('kills');
            $table->integer('champ_level');
            $table->json('challenges')->nullable();
            $table->json('stats');
            $table->integer('minions_killed');
            $table->integer('largest_killing_spree');
            $table->foreignIdFor(\App\Models\Champion::class, 'champion_id')->index()->constrained();
            $table->foreignIdFor(\App\Models\Summoner::class, 'summoner_id')->index()->constrained();
            $table->foreignIdFor(\App\Models\Matche::class, 'match_id')->index()->constrained()->onDelete('cascade');
            $table->unsignedInteger('double_kills');
            $table->unsignedInteger('triple_kills');
            $table->unsignedInteger('quadra_kills');
            $table->unsignedInteger('penta_kills');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('summoner_matches');
    }
};
