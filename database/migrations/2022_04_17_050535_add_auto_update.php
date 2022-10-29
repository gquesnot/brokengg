<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAutoUpdate extends Migration
{
    public function up()
    {
        Schema::table('summoners', function (Blueprint $table) {
            $table->boolean('auto_update')->default(false);
        });
    }

    public function down()
    {
        Schema::table('summoners', function (Blueprint $table) {
            //
        });
    }
}
