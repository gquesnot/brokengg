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
        Schema::create('summoners', function (Blueprint $table) {
            $table->id();
            $table->string('summoner_id')->nullable();
            $table->string('account_id')->nullable();
            $table->string('puuid')->nullable();
            $table->string('name')->nullable();
            $table->string('profile_icon_id')->nullable();
            $table->string('revision_date')->nullable();
            $table->string('summoner_level')->nullable();
            $table->string('last_scanned_match')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('summoners');
    }
};
