<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesSicBoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_sic_bo', function (Blueprint $table) {
            // columns
            $table->bigIncrements('id');
            $table->text('bets');
            $table->string('roll', 7); // 3 rolled dice, e.g. [4,1,6]
            $table->unsignedTinyInteger('score'); // roll result
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
        Schema::dropIfExists('games_sic_bo');
    }
}
