<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesMultiplayerRouletteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_multiplayer_roulette', function (Blueprint $table) {
            // columns
            $table->id();
            $table->text('bets');
            $table->unsignedTinyInteger('win_number')->nullable(); // roulette win number
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
        Schema::dropIfExists('games_multiplayer_roulette');
    }
}
