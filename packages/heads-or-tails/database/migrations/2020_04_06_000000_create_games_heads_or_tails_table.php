<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesHeadsOrTailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_heads_or_tails', function (Blueprint $table) {
            // columns
            $table->bigIncrements('id');
            $table->tinyInteger('toss_bet')->unsigned(); // 0 - heads, 1 - tails
            $table->tinyInteger('toss_result')->unsigned(); // 0 - heads, 1 - tails
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
        Schema::dropIfExists('games_heads_or_tails');
    }
}
