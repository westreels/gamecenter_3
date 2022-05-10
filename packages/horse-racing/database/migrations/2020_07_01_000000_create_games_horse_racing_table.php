<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesHorseRacingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_horse_racing', function (Blueprint $table) {
            // columns
            $table->bigIncrements('id');
            $table->text('bets'); // player bets
            $table->string('positions', 50); // final horses positions
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
        Schema::dropIfExists('games_horse_racing');
    }
}
