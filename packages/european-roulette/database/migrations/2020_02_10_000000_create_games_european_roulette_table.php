<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesEuropeanRouletteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_european_roulette', function (Blueprint $table) {
            // columns
            $table->bigIncrements('id');
            $table->string('bets', 5000); // player bets, array
            $table->string('wins', 5000); // player wins, array
            $table->unsignedTinyInteger('win_number'); // roulette win number (number 0-36)
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
        Schema::dropIfExists('games_european_roulette');
    }
}
