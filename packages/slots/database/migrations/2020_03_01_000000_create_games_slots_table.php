<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_slots', function (Blueprint $table) {
            // columns
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('variation'); // slots game variation
            $table->unsignedTinyInteger('lines'); // number of bet lines
            $table->string('wins', 5000); // wins
            $table->string('reels', 50); // final reel positions, e.g. [9,27,26,23,15]
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
        Schema::dropIfExists('games_slots');
    }
}
