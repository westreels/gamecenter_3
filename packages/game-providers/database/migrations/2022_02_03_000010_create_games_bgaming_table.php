<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesBgamingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_bgaming', function (Blueprint $table) {
            // columns
            $table->id();
            $table->string('game_id', 100);
            $table->string('name', 300);
            $table->string('external_id', 50)->unique();
            $table->mediumText('data');
            $table->timestamps();
            // indexes
            $table->index('game_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games_bgaming');
    }
}
