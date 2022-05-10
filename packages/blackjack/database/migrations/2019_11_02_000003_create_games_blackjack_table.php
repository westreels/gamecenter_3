<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesBlackjackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_blackjack', function (Blueprint $table) {
            // columns
            $table->bigIncrements('id');
            $table->string('deck', 280); // comma-separated card deck, e.g. h2,st,da
            $table->string('actions', 100);
            // dealer hand
            $table->string('dealer_hand', 100);
            $table->unsignedTinyInteger('dealer_score');
            $table->boolean('dealer_blackjack')->default(0); // whether dealer has BJ (1) or not (0)
            // player hand
            $table->decimal('bet', 10, 2);
            $table->decimal('win', 10, 2);
            $table->string('player_hand', 100);
            $table->unsignedTinyInteger('player_score');
            $table->boolean('player_blackjack')->default(0); // whether player has BJ (1) or not (0)
            // insurance
            $table->decimal('insurance_bet', 10, 2)->default(0); // insurance bet
            $table->decimal('insurance_win', 10, 2)->default(0); // insurance win
            // 2nd player hand (in case of split)
            $table->decimal('bet2', 10, 2)->default(0);
            $table->decimal('win2', 10, 2)->default(0);
            $table->string('player_hand2', 100)->nullable();
            $table->unsignedTinyInteger('player_score2')->default(0);

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
        Schema::dropIfExists('games_blackjack');
    }
}
