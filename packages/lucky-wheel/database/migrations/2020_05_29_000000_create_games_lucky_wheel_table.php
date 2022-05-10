<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesLuckyWheelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_lucky_wheel', function (Blueprint $table) {
            // columns
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('variation'); // game variation
            $table->unsignedTinyInteger('position'); // wheel position index
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
        Schema::dropIfExists('games_lucky_wheel');
    }
}
