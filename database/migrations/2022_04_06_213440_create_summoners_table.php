<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
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
            $table->string('name')->index()->nullable();
            $table->unsignedBigInteger('profile_icon_id')->nullable();
            $table->unsignedBigInteger('revision_date')->nullable();
            $table->unsignedBigInteger('summoner_level')->nullable();
            $table->string('last_scanned_match')->nullable();
            $table->boolean('complete')->default(false);
            $table->string('summoner_id')->unique()->index()->nullable();
            $table->string('account_id')->unique()->index()->nullable();
            $table->string('puuid')->unique()->index()->nullable();
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
