<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesEvoplayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_evoplay', function (Blueprint $table) {
            // columns
            $table->id();
            $table->unsignedInteger('game_id');
            $table->string('name', 300);
            $table->string('external_id', 50)->unique(); // unique identifier of all callbacks within particular game round
            $table->mediumText('data');
            $table->timestamps();
            // indexes
            $table->index('game_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games_evoplay');
    }
}
