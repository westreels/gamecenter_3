<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMultiplayerGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multiplayer_games', function (Blueprint $table) {
            // columns
            $table->id();
            $table->foreignId('provably_fair_game_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->morphs('gameable'); // polymorphic relation (index is created automatically)
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->timestamps();
            // indexes
            $table->index('end_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('multiplayer_games');
    }
}
