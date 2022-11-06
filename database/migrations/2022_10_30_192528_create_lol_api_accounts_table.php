<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('api_accounts', function (Blueprint $table) {
            $table->id();

            $table->string('username');
            $table->string('password');
            $table->boolean('actif')->default(false);
            $table->string('api_key')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lol_api_accounts');
    }
};