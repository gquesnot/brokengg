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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->boolean('updated')->default(false);
            $table->string('match_id');
            $table->foreignIdFor(\App\Models\Mode::class, 'mode_id')->nullable()->constrained();
            $table->foreignIdFor(\App\Models\Map::class, 'map_id')->nullable()->constrained();
            $table->foreignIdFor(\App\Models\Queue::class, 'queue_id')->nullable()->constrained();
            $table->timestamp('match_creation')->nullable();
            $table->time('match_duration')->nullable();
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
        Schema::dropIfExists('matches');
    }
};
