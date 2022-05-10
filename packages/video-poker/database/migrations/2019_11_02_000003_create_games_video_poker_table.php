<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesVideoPokerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_video_poker', function (Blueprint $table) {
            // columns
            $table->bigIncrements('id');
            $table->string('deck', 280); // comma-separated card deck, e.g. h2,st,da
            $table->string('hold', 15)->nullable(); // comma-separated list of cards indexes to hold
            $table->string('hand', 30)->nullable(); // comma-separated list of cards, final player hand
            $table->unsignedTinyInteger('combination')->default(0);
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
        Schema::dropIfExists('games_video_poker');
    }
}
