<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesCasinoHoldemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_casino_holdem', function (Blueprint $table) {
            // columns
            $table->bigIncrements('id');

            $table->string('deck', 280); // comma-separated card deck, e.g. ["h2","st","da",...]
            // bets and wins
            // ante_bet + call_bet + bonus_bet = games.bet
            // ante_win + call_win + bonus_win = games.win
            $table->unsignedInteger('ante_bet');
            $table->unsignedInteger('ante_win')->default(0);
            $table->unsignedInteger('call_bet')->default(0);
            $table->unsignedInteger('call_win')->default(0);
            $table->unsignedInteger('bonus_bet');
            $table->unsignedInteger('bonus_win')->default(0);
            // initial draw
            $table->string('player_cards', 11); // 2 cards
            $table->string('dealer_cards', 11)->default('[]'); // 2 cards
            $table->string('community_cards', 26); // 5 cards
            // player and dealer hands
            $table->string('player_hand', 26)->default('[]'); // 5 cards
            $table->string('player_bonus_hand', 26)->default('[]'); // 5 cards
            $table->string('dealer_hand', 26)->default('[]'); // 5 cards
            // player and dealer kickers
            $table->string('player_kickers', 22)->default('[]'); // 0-4 cards
            $table->string('dealer_kickers', 22)->default('[]'); // 0-4 cards
            // player and dealer hand codes
            $table->unsignedTinyInteger('player_hand_rank')->default(0);
            $table->unsignedTinyInteger('player_bonus_hand_rank')->default(0);
            $table->unsignedTinyInteger('dealer_hand_rank')->default(0);
            $table->boolean('dealer_qualified')->default(FALSE);

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
        Schema::dropIfExists('games_casino_holdem');
    }
}
