<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesMultiplayerBlackjackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_multiplayer_blackjack', function (Blueprint $table) {
            // columns
            $table->id();
            $table->string('deck', 280); // comma-separated card deck, e.g. h2,st,da
            $table->text('hands');
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
        Schema::dropIfExists('games_multiplayer_blackjack');
    }
}
