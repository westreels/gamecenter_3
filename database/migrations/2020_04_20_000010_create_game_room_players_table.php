<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameRoomPlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_room_players', function (Blueprint $table) {
            // columns
            $table->id();
            $table->foreignId('game_room_id');
            $table->foreignId('user_id');
            $table->foreignId('game_id')->nullable();
            $table->timestamps();
            // foreign keys
            $table->foreign('game_room_id')->references('id')->on('game_rooms')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('game_id')->references('id')->on('games')->onUpdate('cascade')->onDelete('cascade');
            // indexes
            $table->unique(['game_room_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_room_players');
    }
}
