<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesBaccaratTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_baccarat', function (Blueprint $table) {
            // columns
            $table->bigIncrements('id');
            $table->string('deck', 280);
            $table->unsignedTinyInteger('bet_type');
            // player hand
            $table->string('player_hand', 30);
            $table->unsignedTinyInteger('player_score');
            // banker hand
            $table->string('banker_hand', 30);
            $table->unsignedTinyInteger('banker_score');
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
        Schema::dropIfExists('games_baccarat');
    }
}
