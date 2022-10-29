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
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->string('map');
            $table->string('description');
            $table->timestamps();
        });
        Schema::create('modes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->timestamps();
        });
        Schema::create('champions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title');
            $table->string('img_url');
            $table->string('champion_id');
            $table->timestamps();
        });
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('img_url');
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
        Schema::dropIfExists('queues');
        Schema::dropIfExists('modes');
        Schema::dropIfExists('champions');
        Schema::dropIfExists('items');
    }
};
